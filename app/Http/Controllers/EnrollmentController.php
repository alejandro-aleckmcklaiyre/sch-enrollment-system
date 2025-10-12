<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        $query = Enrollment::with('student');

        if ($search = $request->query('search')) {
            $query->whereHas('student', function($q) use ($search) {
                $q->where('last_name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('student_no', 'like', "%{$search}%");
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $enrollments = $query->orderBy('date_enrolled', 'desc')->paginate($perPage)->withQueryString();

        $students = Student::orderBy('last_name')->get();

        return view('enrollments.index', compact('enrollments','students'));
    }

    public function store(Request $request)
    {
        $data = $request->only(['student_id','section_id','date_enrolled','status','letter_grade']);

        $validator = Validator::make($data, [
            'student_id' => 'required|exists:tblstudent,student_id',
            'section_id' => 'required|integer',
            'date_enrolled' => 'required|date',
            'status' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Enrollment::create($data);

        return response()->json(['message' => 'Enrollment created']);
    }

    public function update(Request $request, $id)
    {
        $en = Enrollment::findOrFail($id);
        $data = $request->only(['student_id','section_id','date_enrolled','status','letter_grade']);

        $validator = Validator::make($data, [
            'student_id' => 'required|exists:tblstudent,student_id',
            'section_id' => 'required|integer',
            'date_enrolled' => 'required|date',
            'status' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $en->update($data);

        return response()->json(['message' => 'Enrollment updated']);
    }

    public function destroy($id)
    {
        $en = Enrollment::findOrFail($id);
        $en->delete();

        return response()->json(['message' => 'Enrollment deleted']);
    }

    public function exportExcel(Request $request)
    {
        $query = Enrollment::with('student');
        // support both GET and POST filter inputs
        $search = $request->input('search', $request->query('search'));
        $status = $request->input('status', $request->query('status'));

        if ($search) {
            $query->whereHas('student', function($q) use ($search) {
                $q->where('last_name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('student_no', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $items = $query->orderBy('date_enrolled', 'desc')->get();

        $filename = 'enrollments_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($items) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Student No','Name','Section ID','Date Enrolled','Status','Letter Grade']);
            foreach ($items as $i) {
                $name = trim(($i->student->last_name ?? '') . ', ' . ($i->student->first_name ?? ''));
                fputcsv($out, [($i->student->student_no ?? ''), $name, $i->section_id, $i->date_enrolled, $i->status, $i->letter_grade]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPDF(Request $request)
    {
        $query = Enrollment::with('student');
        if ($search = $request->query('search')) {
            $query->whereHas('student', function($q) use ($search) {
                $q->where('last_name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%");
            });
        }
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $items = $query->orderBy('date_enrolled', 'desc')->get();

        $pdf = Pdf::loadView('enrollments.export_pdf', compact('items'));
        return $pdf->download('enrollments.pdf');
    }
}
