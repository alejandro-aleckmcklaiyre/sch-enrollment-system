<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Traits\HandlesExports;
use Maatwebsite\Excel\Facades\Excel;

class CourseController extends Controller
{
    use HandlesExports;
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

        $query = Course::with('department');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('course_code', 'like', "%{$search}%")
                  ->orWhere('course_title', 'like', "%{$search}%");
            });
        }
        if ($dept) $query->where('dept_id', $dept);

        $records = $this->getFilteredRecordsForExport($request, $query, Course::class);

        try {
            if (class_exists('\Maatwebsite\Excel\Facades\Excel')) {
                $export = new \App\Exports\CoursesExport($records);
                return Excel::download($export, $this->getExportFilename('courses', 'xlsx'));
            }
        } catch (\Throwable $e) {
            \Log::warning('Courses Excel export failed, falling back to CSV: ' . $e->getMessage());
        }

        $headers = ['ID','Course Code','Course Title','Units','Lecture Hours','Lab Hours','Department'];
        
        return $this->downloadCsv('courses', function($file) use ($records, $headers) {
            fputcsv($file, $headers);
            foreach ($records as $record) {
                fputcsv($file, [
                    $record->course_id,
                    $record->course_code,
                    $record->course_title,
                    $record->units,
                    $record->lecture_hours,
                    $record->lab_hours,
                    optional($record->department)->dept_name ?? $record->dept_id,
                ]);
            }
        }, 'Course Records');
    }

    public function exportPDF(Request $request)
    {
        // Get records sorted by ID ascending
        $records = $this->getOrderedRecords(Course::class, ['department']);
        
        // Prepare view data
        $viewData = [
            'courses' => $records,
            'headers' => ['ID', 'Code', 'Title', 'Units', 'Lecture Hrs', 'Lab Hrs', 'Department'],
            'columns' => [
                'course_id',
                'course_code',
                'course_title',
                'units',
                'lecture_hours',
                'lab_hours',
                ['relation' => 'department', 'field' => 'dept_name']
            ],
            'logoDataUri' => $this->getLogoDataUri()
        ];
        
        // Load PDF view
        $pdf = Pdf::loadView('courses.export_pdf', $viewData);
        
        // Apply standard footer with page numbers
        $this->applyPdfFooter($pdf);
        
        // Generate filename and download
        return $pdf->download($this->getExportFilename('courses', 'pdf'));
    }
}
