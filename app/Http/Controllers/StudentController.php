<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Traits\HandlesExports;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    use HandlesExports;
    public function index(Request $request)
    {
    $query = Student::with('program');

        // sorting
        $allowedSorts = ['student_id','student_no','last_name','first_name','email','year_level','birthdate'];
        $sortBy = $request->input('sort_by', 'student_id');
        $sortDir = strtolower($request->input('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'student_id';
        }

        // search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('student_no', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // filter by year_level
        if ($year = $request->input('year_level')) {
            $query->where('year_level', $year);
        }

        // filter by gender
        if ($gender = $request->input('gender')) {
            $query->where('gender', $gender);
        }

        // filter by program
        if ($program = $request->input('program_id')) {
            $query->where('program_id', $program);
        }

        $perPage = $request->input('per_page', 15);

    $students = $query->orderBy($sortBy, $sortDir)->paginate($perPage)->withQueryString();

    // provide programs for the program select in modals
    $programs = \App\Models\Program::orderBy('program_name')->get();

    return view('students.index', compact('students','programs'));
    }

    public function store(Request $request)
    {
        $data = $request->only(['student_no','last_name','first_name','middle_name','email','gender','birthdate','year_level','program_id']);

        $validator = Validator::make($data, [
            'student_no' => [
                'required',
                \Illuminate\Validation\Rule::unique('tblstudent', 'student_no')->where(function ($query) { $query->where('is_deleted', 0); }),
            ],
            'last_name' => 'required|string',
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'email' => [
                'nullable',
                'email',
                \Illuminate\Validation\Rule::unique('tblstudent', 'email')->where(function ($query) { $query->where('is_deleted', 0); }),
            ],
            'gender' => 'nullable|in:M,F',
            'birthdate' => 'nullable|date',
            'year_level' => 'nullable|integer',
            'program_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'op' => 'add', 'success' => false], 422);
        }

        // Duplicate check by student_no (ignore soft-deleted rows)
        if (!empty($data['student_no']) && Student::where('student_no', $data['student_no'])->where('is_deleted', 0)->exists()) {
            return response()->json(['message' => 'A student with that student number already exists in records.', 'op' => 'add', 'success' => false], 409);
        }
        try {
            $student = Student::create($data);
            \Log::info('Student created: ' . $student->student_id);
            return response()->json(['message' => 'Student created', 'op' => 'add', 'success' => true, 'data' => $student]);
        } catch (\Exception $e) {
            \Log::error('Student create failed: ' . $e->getMessage());
            return response()->json(['message' => 'Student create failed', 'op' => 'add', 'success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $data = $request->only(['student_no','last_name','first_name','middle_name','email','gender','birthdate','year_level','program_id']);

        $validator = Validator::make($data, [
            'student_no' => [
                'required',
                \Illuminate\Validation\Rule::unique('tblstudent', 'student_no')->ignore($student->student_id, 'student_id')->where(function ($query) { $query->where('is_deleted', 0); }),
            ],
            'last_name' => 'required|string',
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'email' => [
                'nullable',
                'email',
                \Illuminate\Validation\Rule::unique('tblstudent', 'email')->ignore($student->student_id, 'student_id')->where(function ($query) { $query->where('is_deleted', 0); }),
            ],
            'gender' => 'nullable|in:M,F',
            'birthdate' => 'nullable|date',
            'year_level' => 'nullable|integer',
            'program_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'op' => 'update', 'success' => false], 422);
        }
        try {
            $student->update($data);
            \Log::info('Student updated: ' . $student->student_id);
            return response()->json(['message' => 'Student updated', 'op' => 'update', 'success' => true, 'data' => $student]);
        } catch (\Exception $e) {
            \Log::error('Student update failed: ' . $e->getMessage());
            return response()->json(['message' => 'Student update failed', 'op' => 'update', 'success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        try {
            $student->delete();
            \Log::info('Student deleted: ' . $student->student_id);
            return response()->json(['message' => 'Student deleted', 'op' => 'delete', 'success' => true]);
        } catch (\Exception $e) {
            \Log::error('Student delete failed: ' . $e->getMessage());
            return response()->json(['message' => 'Student delete failed', 'op' => 'delete', 'success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // Export to Excel
    public function exportExcel(Request $request)
    {
        // Get records sorted by ID ascending with program relation
        $students = $this->getOrderedRecords(Student::class, ['program']);
        
        try {
            // Try to use Maatwebsite Excel export
            if (class_exists('\Maatwebsite\Excel\Facades\Excel')) {
                $export = new \App\Exports\StudentsExport($students);
                return Excel::download($export, $this->getExportFilename('students', 'xlsx'));
            }
        } catch (\Throwable $e) {
            \Log::warning('Excel export failed, falling back to CSV: ' . $e->getMessage());
        }

        // Fallback to CSV if Excel export fails
        $headers = ['ID', 'Student No', 'Last Name', 'First Name', 'Middle Name', 'Email', 'Gender', 'Birthdate', 'Year Level', 'Program'];

        return $this->downloadCsv('students', function($file) use ($students, $headers) {
            fputcsv($file, $headers);
            // Data rows
            foreach ($students as $s) {
                fputcsv($file, [
                    $s->student_id,
                    $s->student_no,
                    $s->last_name,
                    $s->first_name,
                    $s->middle_name,
                    $s->email,
                    $s->gender,
                    $s->birthdate,
                    $s->year_level,
                    optional($s->program)->program_name,
                ]);
            }
        }, 'Student Records');
    }

    // Export to PDF
    public function exportPDF(Request $request)
    {
        // Get records sorted by ID ascending
        $students = $this->getOrderedRecords(Student::class, ['program']);
        
        // Prepare view data
        $viewData = [
            'students' => $students,
            'headers' => ['ID', 'Student No', 'Name', 'Email', 'Gender', 'Birthdate', 'Year', 'Program'],
            'columns' => [
                'student_id',
                'student_no',
                ['callback' => function($s) { return $s->last_name . ', ' . $s->first_name; }],
                'email',
                'gender',
                'birthdate',
                'year_level',
                ['relation' => 'program', 'field' => 'program_name']
            ],
            'logoDataUri' => $this->getLogoDataUri()
        ];
        
        // Load PDF view
        $pdf = Pdf::loadView('students.export_pdf', $viewData);
        
        // Apply standard footer with page numbers
        $this->applyPdfFooter($pdf);
        
        // Generate filename and download
        return $pdf->download($this->getExportFilename('students', 'pdf'));
    }
}
