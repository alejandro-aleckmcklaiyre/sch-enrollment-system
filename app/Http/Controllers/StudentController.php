<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentController extends Controller
{
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
        $filtered = $request->input('filtered', false);
        $search = $request->input('search');
        $year = $request->input('year_level');
        $gender = $request->input('gender');
        $program = $request->input('program_id');

        $query = Student::query();
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('student_no', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }
        if ($year) {
            $query->where('year_level', $year);
        }
        if ($gender) {
            $query->where('gender', $gender);
        }
        if ($program) {
            $query->where('program_id', $program);
        }

    // support sort params for export as well
    $allowedSorts = ['student_id','student_no','last_name','first_name','email','year_level','birthdate'];
    $sortBy = $request->input('sort_by', 'student_id');
    $sortDir = strtolower($request->input('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';
    if (!in_array($sortBy, $allowedSorts)) $sortBy = 'student_id';

    $students = $filtered ? $query->orderBy($sortBy, $sortDir)->get() : Student::orderBy($sortBy, $sortDir)->get();

        // Fallback: generate CSV so we don't require PhpSpreadsheet in this environment.
        $filename = 'students.csv';
    // include ID column
    $headers = ['ID','Student No','Last Name','First Name','Middle Name','Email','Gender','Birthdate','Year Level','Program'];

        $callback = function() use ($students, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
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
            fclose($file);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    // Export to PDF
    public function exportPDF(Request $request)
    {
        $filtered = $request->input('filtered', false);
        $search = $request->input('search');
        $year = $request->input('year_level');
        $gender = $request->input('gender');
        $program = $request->input('program_id');

        $query = Student::query();
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('student_no', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }
        if ($year) {
            $query->where('year_level', $year);
        }
        if ($gender) {
            $query->where('gender', $gender);
        }
        if ($program) {
            $query->where('program_id', $program);
        }

        $allowedSorts = ['student_id','student_no','last_name','first_name','email','year_level','birthdate'];
        $sortBy = $request->input('sort_by', 'student_id');
        $sortDir = strtolower($request->input('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'student_id';

        $students = $filtered ? $query->orderBy($sortBy, $sortDir)->get() : Student::orderBy($sortBy, $sortDir)->get();

    $pdf = Pdf::loadView('students.export_pdf', ['students' => $students]);

        return $pdf->download('students.pdf');
    }
}
