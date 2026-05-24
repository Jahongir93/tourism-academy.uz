<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vacancy;
use App\Models\VacancyApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VacancyApplicationsExport;

class VacancyApplicationController extends Controller
{
    /**
     * Display a listing of applications.
     */
    public function index(Request $request)
    {
        $query = VacancyApplication::with(['vacancy', 'reviewer']);

        // Filter by vacancy
        if ($request->filled('vacancy_id')) {
            $query->where('vacancy_id', $request->vacancy_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $applications = $query->latest()->paginate(20)->withQueryString();
        $vacancies = Vacancy::select('id', 'title')->orderBy('title')->get();
        $statuses = VacancyApplication::STATUSES;

        // Statistics
        $stats = [
            'total' => VacancyApplication::count(),
            'new' => VacancyApplication::where('status', 'new')->count(),
            'reviewed' => VacancyApplication::where('status', 'reviewed')->count(),
            'shortlisted' => VacancyApplication::where('status', 'shortlisted')->count(),
            'hired' => VacancyApplication::where('status', 'hired')->count(),
        ];

        return view('admin.vacancies.applications.index', compact('applications', 'vacancies', 'statuses', 'stats'));
    }

    /**
     * Display the specified application.
     */
    public function show(VacancyApplication $application)
    {
        $application->load(['vacancy', 'reviewer', 'responseSender']);

        // Mark as reviewed if new
        if ($application->status === 'new') {
            $application->markAsReviewed();
        }

        return view('admin.vacancies.applications.show', compact('application'));
    }

    /**
     * Update application status.
     */
    public function updateStatus(Request $request, VacancyApplication $application)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(VacancyApplication::STATUSES)),
            'internal_notes' => 'nullable|string',
        ]);

        $application->updateStatus($request->status);

        if ($request->filled('internal_notes')) {
            $application->update(['internal_notes' => $request->internal_notes]);
        }

        return back()->with('success', 'Ariza holati yangilandi!');
    }

    /**
     * Send response to applicant.
     */
    public function sendResponse(Request $request, VacancyApplication $application)
    {
        $request->validate([
            'response_message' => 'required|string|min:10',
            'status' => 'nullable|in:' . implode(',', array_keys(VacancyApplication::STATUSES)),
        ]);

        // Update status if provided
        if ($request->filled('status')) {
            $application->updateStatus($request->status);
        }

        // Save response
        $application->sendResponse($request->response_message);

        // Send email
        try {
            Mail::send('emails.vacancy-response', [
                'application' => $application,
                'message' => $request->response_message,
            ], function ($mail) use ($application) {
                $mail->to($application->email, $application->full_name)
                     ->subject('Vakansiya arizangiz bo\'yicha javob - Tourism Academy');
            });
        } catch (\Exception $e) {
            // Log error but don't fail
            \Log::error('Failed to send vacancy response email: ' . $e->getMessage());
        }

        return back()->with('success', 'Javob muvaffaqiyatli yuborildi!');
    }

    /**
     * Export applications to Excel.
     */
    public function export(Request $request)
    {
        $query = VacancyApplication::with(['vacancy']);

        // Apply filters
        if ($request->filled('vacancy_id')) {
            $query->where('vacancy_id', $request->vacancy_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $applications = $query->latest()->get();

        // Generate CSV
        $filename = 'vacancy_applications_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($applications) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header row
            fputcsv($file, [
                'ID',
                'Vakansiya',
                'F.I.O',
                'Email',
                'Telefon',
                'Ma\'lumoti',
                'Tajriba (yil)',
                'Holat',
                'Ariza sanasi',
            ]);

            // Data rows
            foreach ($applications as $app) {
                fputcsv($file, [
                    $app->id,
                    $app->vacancy->title ?? '-',
                    $app->full_name,
                    $app->email,
                    $app->phone,
                    $app->education_level_label,
                    $app->experience_years ?? '-',
                    $app->status_label,
                    $app->created_at->format('d.m.Y H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Delete application.
     */
    public function destroy(VacancyApplication $application)
    {
        // Delete files
        if ($application->resume_path) {
            \Storage::delete($application->resume_path);
        }
        if ($application->photo_path) {
            \Storage::delete($application->photo_path);
        }

        $application->delete();

        return redirect()->route('admin.vacancy-applications.index')
            ->with('success', 'Ariza o\'chirildi!');
    }

    /**
     * Bulk update status.
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:vacancy_applications,id',
            'status' => 'required|in:' . implode(',', array_keys(VacancyApplication::STATUSES)),
        ]);

        VacancyApplication::whereIn('id', $request->ids)->update([
            'status' => $request->status,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', count($request->ids) . ' ta ariza holati yangilandi!');
    }
}
