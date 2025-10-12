<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::with('department');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('course_code', 'like', "%{$search}%")
                  ->orWhere('course_title', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 15);

        $courses = $query->orderBy('course_id', 'desc')->paginate($perPage)->withQueryString();

        $departments = \App\Models\Department::orderBy('dept_name')->get();

        return view('courses.index', compact('courses','departments'));
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'course_code' => 'required|string|unique:tblcourse,course_code',
            'course_title' => 'required|string',
            'units' => 'nullable|integer',
            'lecture_hours' => 'nullable|integer',
            'lab_hours' => 'nullable|integer',
            'dept_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Course::create($data);

        return response()->json(['message' => 'Course created']);
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        $data = $request->all();

        $validator = Validator::make($data, [
            'course_code' => 'required|string|unique:tblcourse,course_code,' . $course->course_id . ',course_id',
            'course_title' => 'required|string',
            'units' => 'nullable|integer',
            'lecture_hours' => 'nullable|integer',
            'lab_hours' => 'nullable|integer',
            'dept_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $course->update($data);

        return response()->json(['message' => 'Course updated']);
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();

        return response()->json(['message' => 'Course deleted']);
    }

    public function exportExcel(Request $request)
    {
        $filtered = $request->input('filtered', false);
        $search = $request->input('search');

        $query = Course::with('department');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('course_code', 'like', "%{$search}%")
                  ->orWhere('course_title', 'like', "%{$search}%");
            });
        }

        $courses = $filtered ? $query->orderBy('course_id','desc')->get() : Course::with('department')->orderBy('course_id','desc')->get();

        $filename = 'courses.csv';
        $headers = ['ID','Course Code','Course Title','Units','Lecture Hours','Lab Hours','Department'];

        $callback = function() use ($courses, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            foreach ($courses as $c) {
                fputcsv($file, [
                    $c->course_id,
                    $c->course_code,
                    $c->course_title,
                    $c->units,
                    $c->lecture_hours,
                    $c->lab_hours,
                    optional($c->department)->dept_name ?? $c->dept_id,
                ]);
            }
            fclose($file);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function exportPDF(Request $request)
    {
        $filtered = $request->input('filtered', false);
        $search = $request->input('search');

        $query = Course::with('department');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('course_code', 'like', "%{$search}%")
                  ->orWhere('course_title', 'like', "%{$search}%");
            });
        }

        $courses = $filtered ? $query->orderBy('course_id','desc')->get() : Course::with('department')->orderBy('course_id','desc')->get();

        $pdf = Pdf::loadView('courses.export_pdf', ['courses' => $courses]);

        return $pdf->download('courses.pdf');
    }
}
