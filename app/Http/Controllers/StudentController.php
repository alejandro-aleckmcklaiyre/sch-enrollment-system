<?php

namespace App\Http\Controllers;

use App\Models\Student;
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

        $perPage = $request->input('per_page', 15);

    $students = $query->orderBy('student_id', 'desc')->paginate($perPage)->withQueryString();

    // provide programs for the program select in modals
    $programs = \App\Models\Program::orderBy('program_name')->get();

    return view('students.index', compact('students','programs'));
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'student_no' => 'required|unique:tblstudent,student_no',
            'last_name' => 'required|string',
            'first_name' => 'required|string',
            'email' => 'nullable|email|unique:tblstudent,email',
            'gender' => 'nullable|in:M,F',
            'birthdate' => 'nullable|date',
            'year_level' => 'nullable|integer',
            'program_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Student::create($data);

        return response()->json(['message' => 'Student created']);
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $data = $request->all();

        $validator = Validator::make($data, [
            'student_no' => 'required|unique:tblstudent,student_no,' . $student->student_id . ',student_id',
            'last_name' => 'required|string',
            'first_name' => 'required|string',
            'email' => 'nullable|email|unique:tblstudent,email,' . $student->student_id . ',student_id',
            'gender' => 'nullable|in:M,F',
            'birthdate' => 'nullable|date',
            'year_level' => 'nullable|integer',
            'program_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $student->update($data);

        return response()->json(['message' => 'Student updated']);
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return response()->json(['message' => 'Student deleted']);
    }

    // Export to Excel
    public function exportExcel(Request $request)
    {
        $filtered = $request->input('filtered', false);
        $search = $request->input('search');
        $year = $request->input('year_level');

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

        $students = $filtered ? $query->orderBy('student_id', 'desc')->get() : Student::orderBy('student_id', 'desc')->get();

        // Fallback: generate CSV so we don't require PhpSpreadsheet in this environment.
        $filename = 'students.csv';
        $headers = ['Student No','Last Name','First Name','Middle Name','Email','Gender','Birthdate','Year Level','Program'];

        $callback = function() use ($students, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            foreach ($students as $s) {
                fputcsv($file, [
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

        $students = $filtered ? $query->orderBy('student_id', 'desc')->get() : Student::orderBy('student_id', 'desc')->get();

    $pdf = Pdf::loadView('students.export_pdf', ['students' => $students]);

        return $pdf->download('students.pdf');
    }
}
