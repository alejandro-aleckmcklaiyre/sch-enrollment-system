<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Traits\HandlesExports;
use Maatwebsite\Excel\Facades\Excel;

class EnrollmentController extends Controller
{
    use HandlesExports;
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
    $query = Enrollment::with(['student','section']);

        $allowedSorts = ['enrollment_id','date_enrolled','status'];
        $sortBy = $request->input('sort_by', 'date_enrolled');
        $sortDir = strtolower($request->input('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'date_enrolled';

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

    $enrollments = $query->orderBy($sortBy, $sortDir)->paginate($perPage)->withQueryString();

        $students = Student::orderBy('last_name')->get();
        // pass distinct section codes and statuses for dropdowns in the view
        $sections = Section::select('section_code')->distinct()->orderBy('section_code')->get();
        // also provide full section rows (for edit dropdowns where a specific section_id is needed)
        $sectionRows = Section::orderBy('section_code')->orderBy('section_id')->get();
        $statuses = Enrollment::getAvailableStatuses();

    return view('enrollments.index', compact('enrollments','students','sections','sectionRows','statuses'));
    }

    public function store(Request $request)
    {
        $studentId = $request->input('student_id');
        $sectionCode = $request->input('section_code'); // we'll receive section_code from the form
        $isIrregular = $request->boolean('is_irregular');
        $selectedSectionRowIds = $request->input('course_section_row_ids', []); // for irregular: array of section row ids
        $dateEnrolled = $request->input('date_enrolled', now());
        $statusInput = $request->input('status');

        $validator = Validator::make([
            'student_id' => $studentId,
            'section_code' => $sectionCode,
            'status' => $statusInput
        ], [
            'student_id' => 'required|exists:tblstudent,student_id',
            'section_code' => 'required|string',
            'status' => 'required|in:enrolled,dropped,completed,irregular'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'op' => 'add', 'success' => false], 422);
        }

        try {
            // Determine section rows to enroll
            if ($isIrregular) {
                // Expect array of section row ids
                if (empty($selectedSectionRowIds) || !is_array($selectedSectionRowIds)) {
                    return response()->json(['message' => 'No courses selected for irregular enrollment', 'success' => false], 422);
                }

                $toEnroll = Section::whereIn('section_id', $selectedSectionRowIds)->where('is_deleted',0)->get();
                if ($toEnroll->isEmpty()) {
                    return response()->json(['message' => 'Selected course/section rows not found', 'success' => false], 404);
                }
                $desiredStatus = 'irregular';
            } else {
                // Regular: enroll into all section rows that share the same section_code
                $toEnroll = Section::where('section_code', $sectionCode)->where('is_deleted',0)->get();
                if ($toEnroll->isEmpty()) {
                    return response()->json(['message' => 'No courses found for selected section code', 'success' => false], 422);
                }
                $desiredStatus = 'enrolled';
            }

            // Check duplicates for any of the enrollments
            $duplicates = [];
            foreach ($toEnroll as $srow) {
                $exists = Enrollment::where('student_id', $studentId)
                            ->where('section_id', $srow->section_id)
                            ->where('course_id', $srow->course_id)
                            ->where('is_deleted',0)
                            ->exists();
                if ($exists) {
                    $duplicates[] = ['section_id' => $srow->section_id, 'course_id' => $srow->course_id];
                }
            }

            if (!empty($duplicates)) {
                return response()->json(['message' => 'Student is already enrolled in one or more selected section-course combinations', 'duplicates' => $duplicates, 'success' => false], 409);
            }

            // Create enrollments
            $created = [];
            foreach ($toEnroll as $srow) {
                $en = Enrollment::create([
                    'student_id' => $studentId,
                    'section_id' => $srow->section_id,
                    'course_id' => $srow->course_id,
                    'date_enrolled' => $dateEnrolled,
                    'status' => $desiredStatus,
                ]);
                $created[] = $en;
            }

            return response()->json(['message' => 'Enrollment(s) created', 'success' => true, 'data' => $created]);
        } catch (\Exception $e) {
            \Log::error('Enrollment create failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to create enrollment. Please try again.', 'op' => 'add', 'success' => false], 500);
        }
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
            return response()->json(['errors' => $validator->errors(), 'op' => 'update', 'success' => false], 422);
        }
        try {
            $en->update($data);
            return response()->json(['message' => 'Enrollment updated', 'op' => 'update', 'success' => true, 'data' => $en]);
        } catch (\Exception $e) {
            \Log::error('Enrollment update failed: ' . $e->getMessage());
            return response()->json(['message' => 'Enrollment update failed', 'op' => 'update', 'success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $en = Enrollment::findOrFail($id);
        try {
            $en->delete();
            return response()->json(['message' => 'Enrollment deleted', 'op' => 'delete', 'success' => true]);
        } catch (\Exception $e) {
            \Log::error('Enrollment delete failed: ' . $e->getMessage());
            return response()->json(['message' => 'Enrollment delete failed', 'op' => 'delete', 'success' => false, 'error' => $e->getMessage()], 500);
        }
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

        // Get filtered & ordered items for export (defaults to date_enrolled for enrollments)
        $items = $this->getFilteredRecordsForExport($request, $query, Enrollment::class, 'date_enrolled');

        try {
            if (class_exists('\Maatwebsite\Excel\Facades\Excel')) {
                $export = new \App\Exports\EnrollmentExport($items);
                return Excel::download($export, $this->getExportFilename('enrollments', 'xlsx'));
            }
        } catch (\Throwable $e) {
            \Log::warning('Enrollment Excel export failed, falling back to CSV: ' . $e->getMessage());
        }

        $callback = function($file) use ($items) {
            fputcsv($file, ['Student No','Name','Section ID','Date Enrolled','Status','Letter Grade']);
            foreach ($items as $i) {
                $name = trim(($i->student->last_name ?? '') . ', ' . ($i->student->first_name ?? ''));
                fputcsv($file, [($i->student->student_no ?? ''), $name, $i->section_id, $i->date_enrolled, $i->status, $i->letter_grade]);
            }
        };

        return $this->downloadCsv('enrollments.csv', $callback, 'Enrollment Records');
    }

    public function exportPDF(Request $request)
    {
        $query = Enrollment::with('student');
        $search = $request->input('search', $request->query('search'));
        $status = $request->input('status', $request->query('status'));

        if ($search) {
            $query->whereHas('student', function($q) use ($search) {
                $q->where('last_name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%");
            });
        }
        if ($status) {
            $query->where('status', $status);
        }

        // enforce ascending order / filtered items for export
        $items = $this->getFilteredRecordsForExport($request, $query, Enrollment::class, 'date_enrolled');

        // Prepare logo
        $logoDataUri = $this->getLogoDataUri();

        // Load PDF view
        $pdf = Pdf::loadView('enrollments.export_pdf', compact('items') + ['logoDataUri' => $logoDataUri]);

        // Apply standard footer
        $this->applyPdfFooter($pdf);

        return $pdf->download($this->getExportFilename('enrollments', 'pdf'));
    }

    /**
     * Return all section rows (courses) for a given section identifier (id or code)
     */
    public function getSectionCourses($identifier)
    {
        if (is_numeric($identifier)) {
            $section = Section::findOrFail($identifier);
            $sectionCode = $section->section_code;
        } else {
            $sectionCode = urldecode($identifier);
        }

        $rows = Section::where('section_code', $sectionCode)->where('is_deleted', 0)->get();

        $courses = $rows->map(function($r){
            return [
                'section_id' => $r->section_id,
                'course_id' => $r->course_id,
                'course_code' => optional($r->course)->course_code,
                'course_title' => optional($r->course)->course_title,
                'instructor_name' => optional($r->instructor)->last_name ? optional($r->instructor)->last_name . ', ' . optional($r->instructor)->first_name : null,
                'room_code' => optional($r->room)->room_code,
                'day_pattern' => $r->day_pattern,
                'start_time' => $r->start_time,
                'end_time' => $r->end_time,
            ];
        });

        return response()->json(['courses' => $courses, 'success' => true]);
    }
}
