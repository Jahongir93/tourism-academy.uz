<?php

namespace App\Http\Controllers\CampusTour;

use App\Http\Controllers\Controller;
use App\Http\Requests\CampusTour\TransportRouteRequest;
use App\Models\CampusTour\TransportRoute;
use Illuminate\Http\Request;

class TransportRouteController extends Controller
{
    public function index(Request $request)
    {
        $query = TransportRoute::ordered();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('start_point', 'like', "%{$search}%")
                  ->orWhere('end_point', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->ofType($request->type);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $routes = $query->paginate(15);
        $types = TransportRoute::TYPES;

        return view('campus-tour.admin.routes.index', compact('routes', 'types'));
    }

    public function create()
    {
        $types = TransportRoute::TYPES;
        return view('campus-tour.admin.routes.create', compact('types'));
    }

    public function store(TransportRouteRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        TransportRoute::create($data);

        return redirect()->route('campus-tour.routes.index')
            ->with('success', 'Yo\'nalish muvaffaqiyatli qo\'shildi!');
    }

    public function show(TransportRoute $route)
    {
        return view('campus-tour.admin.routes.show', compact('route'));
    }

    public function edit(TransportRoute $route)
    {
        $types = TransportRoute::TYPES;
        return view('campus-tour.admin.routes.edit', compact('route', 'types'));
    }

    public function update(TransportRouteRequest $request, TransportRoute $route)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        $route->update($data);

        return redirect()->route('campus-tour.routes.index')
            ->with('success', 'Yo\'nalish muvaffaqiyatli yangilandi!');
    }

    public function destroy(TransportRoute $route)
    {
        $route->delete();

        return redirect()->route('campus-tour.routes.index')
            ->with('success', 'Yo\'nalish o\'chirildi!');
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:campus_tour_routes,id',
            'items.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->items as $item) {
            TransportRoute::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json(['success' => true]);
    }
}
