<?php

namespace App\Http\Controllers\LMS;

use App\Http\Controllers\Controller;
use App\Models\LmsPracticeTest;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;

class LmsTestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tests = LmsPracticeTest::with(['subject', 'teacher'])
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        $subjects = Subject::where('is_active', true)->orderBy('name_uz')->get();
            
        return view('lms.tests.index', compact('tests', 'subjects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $subjects = Subject::where('is_active', true)->orderBy('name_uz')->get();
        return view('lms.tests.create', compact('subjects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $test = LmsPracticeTest::findOrFail($id);
        return view('lms.tests.show', compact('test'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $test = LmsPracticeTest::findOrFail($id);
        return view('lms.tests.edit', compact('test'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    
    /**
     * Take test
     */
    public function take(string $id)
    {
        $test = LmsPracticeTest::findOrFail($id);
        return view('lms.tests.take', compact('test'));
    }

    /**
     * Show import form
     */
    public function showImportForm()
    {
        $subjects = Subject::where('is_active', true)->orderBy('name_uz')->get();
        return view('lms.tests.import', compact('subjects'));
    }

    /**
     * Import tests from Excel file
     */
    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240',
            'subject_id' => 'required|exists:subjects,id'
        ]);

        try {
            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $highestRow = $sheet->getHighestRow();

            // Read test info from first rows
            $testTitle = $sheet->getCell('B2')->getValue() ?? 'Yangi test';
            $testDescription = $sheet->getCell('B3')->getValue() ?? '';
            $timeLimit = (int)($sheet->getCell('B4')->getValue() ?? 30);
            $passingScore = (int)($sheet->getCell('B5')->getValue() ?? 60);
            $testType = $sheet->getCell('B6')->getValue() ?? 'practice';

            // Prepare questions array
            $questions = [];

            // Start reading questions from row 10
            for ($row = 10; $row <= $highestRow; $row++) {
                $questionText = trim($sheet->getCell('A' . $row)->getValue() ?? '');

                if (empty($questionText)) {
                    continue;
                }

                $questionType = trim($sheet->getCell('B' . $row)->getValue() ?? 'multiple_choice');
                $points = (int)($sheet->getCell('C' . $row)->getValue() ?? 1);
                $explanation = trim($sheet->getCell('D' . $row)->getValue() ?? '');
                $correctAnswer = trim($sheet->getCell('E' . $row)->getValue() ?? '');

                $question = [
                    'question' => $questionText,
                    'type' => $questionType,
                    'points' => $points,
                    'explanation' => $explanation
                ];

                // Process based on question type
                if ($questionType === 'multiple_choice') {
                    $options = [];
                    foreach (['F', 'G', 'H', 'I'] as $col) {
                        $optionValue = trim($sheet->getCell($col . $row)->getValue() ?? '');
                        if (!empty($optionValue)) {
                            $options[] = $optionValue;
                        }
                    }

                    $correctAnswerLetter = strtoupper($correctAnswer);
                    $correctAnswerIndex = ord($correctAnswerLetter) - ord('A');

                    $question['options'] = $options;
                    $question['correct_answer'] = $correctAnswerIndex;

                } elseif ($questionType === 'true_false') {
                    $correctAnswerLower = strtolower($correctAnswer);
                    $question['correct_answer'] = ($correctAnswerLower === 'true' || $correctAnswerLower === 'to\'g\'ri') ? 'true' : 'false';

                } elseif ($questionType === 'short_answer') {
                    $question['correct_answer'] = $correctAnswer;
                }

                $questions[] = $question;
            }

            if (empty($questions)) {
                return back()->with('error', 'Excel faylida savollar topilmadi!');
            }

            // Create test
            $test = LmsPracticeTest::create([
                'subject_id' => $request->subject_id,
                'teacher_id' => Auth::id(),
                'title' => $testTitle,
                'description' => $testDescription,
                'questions' => $questions,
                'time_limit' => $timeLimit,
                'passing_score' => $passingScore,
                'test_type' => $testType,
                'show_correct_answers' => true,
                'allow_retake' => true,
                'is_active' => true
            ]);

            return redirect()->route('lms.tests.index')->with('success', 'Test muvaffaqiyatli import qilindi! Jami ' . count($questions) . ' ta savol qo\'shildi.');

        } catch (\Exception $e) {
            return back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Download sample Excel template with colors
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Test Import');

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(50);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);

        // ===== TEST INFORMATION SECTION =====
        // Title row
        $sheet->mergeCells('A1:I1');
        $sheet->setCellValue('A1', 'TEST MA\'LUMOTLARI:');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2E86AB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Test info rows with colors
        $infoRows = [
            2 => ['label' => 'Test nomi:', 'value' => 'Matematika - 1-Modul Test', 'color' => 'E8F4F8'],
            3 => ['label' => 'Tavsif:', 'value' => 'Bu test matematika fanining 1-moduli bo\'yicha', 'color' => 'F0F8FF'],
            4 => ['label' => 'Vaqt chegarasi (daqiqa):', 'value' => '30', 'color' => 'E8F4F8'],
            5 => ['label' => 'O\'tish bali (%):', 'value' => '60', 'color' => 'F0F8FF'],
            6 => ['label' => 'Test turi:', 'value' => 'practice', 'color' => 'E8F4F8'],
        ];

        foreach ($infoRows as $row => $data) {
            $sheet->setCellValue('A' . $row, $data['label']);
            $sheet->setCellValue('B' . $row, $data['value']);

            $sheet->getStyle('A' . $row)->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D4E6F1']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]
            ]);

            $sheet->getStyle('B' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $data['color']]]
            ]);
        }

        // Empty row
        $sheet->getRowDimension(7)->setRowHeight(5);

        // ===== QUESTIONS SECTION =====
        // Section title
        $sheet->mergeCells('A8:I8');
        $sheet->setCellValue('A8', 'SAVOLLAR:');
        $sheet->getStyle('A8')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'A23B72']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
        ]);
        $sheet->getRowDimension(8)->setRowHeight(25);

        // Headers row with distinct colors
        $headers = [
            'A9' => ['text' => 'Savol', 'color' => 'FFE5B4'],
            'B9' => ['text' => 'Turi', 'color' => 'FFD1DC'],
            'C9' => ['text' => 'Ball', 'color' => 'E0BBE4'],
            'D9' => ['text' => 'Tushuntirish', 'color' => 'D4F1F4'],
            'E9' => ['text' => 'To\'g\'ri javob', 'color' => 'B4E7CE'],
            'F9' => ['text' => 'A variant', 'color' => 'FFDAB9'],
            'G9' => ['text' => 'B variant', 'color' => 'FFE4B5'],
            'H9' => ['text' => 'C variant', 'color' => 'FFEFD5'],
            'I9' => ['text' => 'D variant', 'color' => 'FFF8DC'],
        ];

        foreach ($headers as $cell => $data) {
            $sheet->setCellValue($cell, $data['text']);
            $sheet->getStyle($cell)->applyFromArray([
                'font' => ['bold' => true, 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $data['color']]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true]
            ]);
        }
        $sheet->getRowDimension(9)->setRowHeight(30);

        // Sample questions with alternating colors
        $sampleQuestions = [
            ['2 + 2 nechaga teng?', 'multiple_choice', '1', 'Oddiy matematik amal', 'C', '3', '5', '4', '6'],
            ['Yer Quyosh atrofida aylanadi', 'true_false', '1', 'Astronomiya asoslari', 'true', '', '', '', ''],
            ['O\'zbekiston poytaxti?', 'short_answer', '1', 'Geografiya', 'Toshkent', '', '', '', ''],
            ['Qaysi davlat eng katta maydonga ega?', 'multiple_choice', '2', '', 'A', 'Rossiya', 'Xitoy', 'AQSh', 'Kanada'],
        ];

        $row = 10;
        foreach ($sampleQuestions as $index => $question) {
            $col = 'A';
            foreach ($question as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }

            // Alternate row colors
            $bgColor = ($index % 2 === 0) ? 'F5F5F5' : 'FFFFFF';
            $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
            ]);
            $row++;
        }

        // Add borders to all data
        $sheet->getStyle('A1:I13')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '333333']
                ]
            ]
        ]);

        // ===== INSTRUCTIONS SECTION =====
        $sheet->getRowDimension(14)->setRowHeight(5);

        $sheet->mergeCells('A15:I15');
        $sheet->setCellValue('A15', 'KO\'RSATMALAR:');
        $sheet->getStyle('A15')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F18F01']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $instructions = [
            '1. Test ma\'lumotlarini (2-6 qatorlar) to\'ldiring',
            '2. Savollarni 10-qatordan boshlab yozing',
            '3. Savol turlari: multiple_choice, true_false, short_answer',
            '4. Ko\'p tanlovli savollarda to\'g\'ri javobni A, B, C yoki D harfi bilan ko\'rsating',
            '5. To\'g\'ri/Noto\'g\'ri savollarda: true yoki false',
            '6. Qisqa javobli savollarda to\'g\'ri javobni yozing',
            '7. Test turi: practice, quiz, midterm, final',
            '8. Har bir ustun rangi bilan ajratilgan - qulaylik uchun',
        ];

        $instRow = 16;
        foreach ($instructions as $instruction) {
            $sheet->mergeCells('A' . $instRow . ':I' . $instRow);
            $sheet->setCellValue('A' . $instRow, $instruction);
            $sheet->getStyle('A' . $instRow)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF3E0']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
            ]);
            $instRow++;
        }

        // Save to temp file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'test-import-template.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}