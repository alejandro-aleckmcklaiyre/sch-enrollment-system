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

    $allowedSorts = ['course_id','course_code','course_title','dept_id','units'];
    $sortBy = $request->input('sort_by', 'course_id');
    // for listing default sort_dir remains desc, but for exports we want asc by default
    $sortDir = strtolower($request->input('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'course_id';

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('course_code', 'like', "%{$search}%")
                  ->orWhere('course_title', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 15);

    $courses = $query->orderBy($sortBy, $sortDir)->paginate($perPage)->withQueryString();

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
            return response()->json(['errors' => $validator->errors(), 'op' => 'add', 'success' => false], 422);
        }
        // Duplicate check by course_code
        if (!empty($data['course_code']) && Course::where('course_code', $data['course_code'])->exists()) {
            return response()->json(['message' => 'A course with that course code already exists in records.', 'op' => 'add', 'success' => false], 409);
        }
        try {
            $course = Course::create($data);
            \Log::info('Course created: ' . $course->course_id);
            return response()->json(['message' => 'Course created', 'op' => 'add', 'success' => true, 'data' => $course]);
        } catch (\Exception $e) {
            \Log::error('Course create failed: ' . $e->getMessage());
            return response()->json(['message' => 'Course create failed', 'op' => 'add', 'success' => false, 'error' => $e->getMessage()], 500);
        }
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
            return response()->json(['errors' => $validator->errors(), 'op' => 'update', 'success' => false], 422);
        }
        try {
            $course->update($data);
            return response()->json(['message' => 'Course updated', 'op' => 'update', 'success' => true, 'data' => $course]);
        } catch (\Exception $e) {
            \Log::error('Course update failed: ' . $e->getMessage());
            return response()->json(['message' => 'Course update failed', 'op' => 'update', 'success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        try {
            $course->delete();
            return response()->json(['message' => 'Course deleted', 'op' => 'delete', 'success' => true]);
        } catch (\Exception $e) {
            \Log::error('Course delete failed: ' . $e->getMessage());
            return response()->json(['message' => 'Course delete failed', 'op' => 'delete', 'success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function exportExcel(Request $request)
    {
        $filtered = $request->input('filtered', false);
        $search = $request->input('search');
        $dept = $request->input('dept_id');

        // sorting
        $allowedSorts = ['course_id','course_code','course_title','dept_id','units'];
        $sortBy = $request->input('sort_by', 'course_id');
        $sortDir = strtolower($request->input('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'course_id';

        $query = Course::with('department');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('course_code', 'like', "%{$search}%")
                  ->orWhere('course_title', 'like', "%{$search}%");
            });
        }
        if ($dept) $query->where('dept_id', $dept);

        // For export, override default to ascending order unless user explicitly requested descending
        $exportSortDir = $request->has('sort_dir') ? $sortDir : 'asc';
        $courses = $filtered ? $query->orderBy($sortBy,$exportSortDir)->get() : Course::with('department')->orderBy($sortBy,$exportSortDir)->get();

        try {
            if (class_exists('\Maatwebsite\Excel\Facades\Excel')) {
                $export = new \App\Exports\CoursesExport($courses);
                return \Maatwebsite\Excel\Facades\Excel::download($export, 'courses.xlsx');
            }
        } catch (\Throwable $e) {
            \Log::warning('Courses Excel export failed, falling back to CSV: ' . $e->getMessage());
        }

        // Fallback to CSV
        $filename = 'courses.csv';
        $headers = ['ID','Course Code','Course Title','Units','Lecture Hours','Lab Hours','Department'];

        $callback = function() use ($courses, $headers) {
            $file = fopen('php://output', 'w');
            // University header lines
            fputcsv($file, ['Polytechnic University of the Philippines – Taguig Campus']);
            fputcsv($file, ['Date created: ' . date('F j, Y')]);
            fputcsv($file, []);
            // Column headers
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
        $records = Course::where('is_deleted', 0)
            ->orderBy('course_id', 'asc')
            ->with('department')
            ->get();

        $pdf = Pdf::loadView('courses.export_pdf', ['courses' => $records])
            ->setPaper('A4', 'portrait');

        $pdf->render(); // ensure footer scripts execute properly

        return $pdf->download('courses.pdf');
    }
}
