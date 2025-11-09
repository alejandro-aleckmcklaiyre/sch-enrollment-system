<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\Room;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Traits\HandlesExports;
use Maatwebsite\Excel\Facades\Excel;

class SectionController extends Controller
{
    use HandlesExports;
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
    $query = Section::with(['course','instructor','room','term']);

    $allowedSorts = ['section_id','section_code','course_id','max_capacity'];
        $sortBy = $request->input('sort_by', 'section_code');
        $sortDir = strtolower($request->input('sort_dir', 'asc')) === 'asc' ? 'asc' : 'desc';
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'section_code';

        if ($search = $request->query('search')) {
            $query->where('section_code','like',"%{$search}%");
        }
    $sections = $query->orderBy($sortBy, $sortDir)->paginate($perPage)->withQueryString();

        $courses = Course::orderBy('course_code')->get();
        $instructors = Instructor::orderBy('last_name')->get();
        $rooms = Room::orderBy('room_code')->get();
        $terms = Term::orderBy('term_code')->get();

        return view('sections.index', compact('sections','courses','instructors','rooms','terms'));
    }

    public function store(Request $request)
    {
        // Allow selecting existing section_code or providing new one
        $payload = $request->all();
        $sectionCode = $request->input('section_code');
        $newSectionCode = $request->input('new_section_code');
        $courseId = $request->input('course_id');

        if (empty($sectionCode) && empty($newSectionCode)) {
            return response()->json(['message' => 'Section code is required', 'op' => 'add', 'success' => false], 422);
        }

        $section_code = $sectionCode ?: $newSectionCode;

        $data = $request->only(['term_id','instructor_id','day_pattern','start_time','end_time','room_id','max_capacity']);
        $data['section_code'] = $section_code;
        $data['course_id'] = $courseId;

        $validator = Validator::make($data, [
            'section_code' => 'required|string|max:20',
            'course_id' => 'required|exists:tblcourse,course_id',
            'term_id' => 'required|exists:tblterm,term_id',
            'instructor_id' => 'required|exists:tblinstructor,instructor_id',
            'room_id' => 'required|exists:tblroom,room_id',
        ]);
        if ($validator->fails()) return response()->json(['errors'=>$validator->errors(), 'op' => 'add', 'success' => false],422);

        // Prevent duplicate section_code + course_id
        $exists = Section::where('section_code', $section_code)
                    ->where('course_id', $courseId)
                    ->where('is_deleted', 0)
                    ->exists();
        if ($exists) {
            return response()->json(['message' => 'This course is already assigned to the selected section code', 'op' => 'add', 'success' => false], 409);
        }

        try {
            $sec = Section::create($data);
            return response()->json(['message'=>'Section created', 'op' => 'add', 'success' => true, 'data' => $sec]);
        } catch (\Exception $e) {
            \Log::error('Section create failed: ' . $e->getMessage());
            return response()->json(['message'=>'Section create failed', 'op' => 'add', 'success' => false, 'error' => $e->getMessage()],500);
        }
    }

    public function update(Request $request, $id)
    {
        $s = Section::findOrFail($id);
        $sectionCode = $request->input('section_code');
        $newSectionCode = $request->input('new_section_code');
        $courseId = $request->input('course_id');

        if (empty($sectionCode) && empty($newSectionCode)) {
            return response()->json(['message' => 'Section code is required', 'op' => 'update', 'success' => false], 422);
        }

        $section_code = $sectionCode ?: $newSectionCode;

        $data = $request->only(['term_id','instructor_id','day_pattern','start_time','end_time','room_id','max_capacity']);
        $data['section_code'] = $section_code;
        $data['course_id'] = $courseId;

        $validator = Validator::make($data, [
            'section_code' => 'required|string|max:20',
            'course_id' => 'required|exists:tblcourse,course_id',
            'term_id' => 'required|exists:tblterm,term_id',
            'instructor_id' => 'required|exists:tblinstructor,instructor_id',
            'room_id' => 'required|exists:tblroom,room_id',
        ]);
        if ($validator->fails()) return response()->json(['errors'=>$validator->errors(), 'op' => 'update', 'success' => false],422);

        // Prevent duplicate section_code + course_id for other records
        $exists = Section::where('section_code', $section_code)
                    ->where('course_id', $courseId)
                    ->where('is_deleted', 0)
                    ->where('section_id', '!=', $s->section_id)
                    ->exists();
        if ($exists) {
            return response()->json(['message' => 'This course is already assigned to the selected section code', 'op' => 'update', 'success' => false], 409);
        }

        try {
            $s->update($data);
            return response()->json(['message'=>'Section updated', 'op' => 'update', 'success' => true, 'data' => $s]);
        } catch (\Exception $e) {
            \Log::error('Section update failed: ' . $e->getMessage());
            return response()->json(['message'=>'Section update failed', 'op' => 'update', 'success' => false, 'error' => $e->getMessage()],500);
        }
    }

    public function destroy($id)
    {
        $s = Section::findOrFail($id);
        try {
            $s->delete();
            return response()->json(['message'=>'Section deleted', 'op' => 'delete', 'success' => true]);
        } catch (\Exception $e) {
            \Log::error('Section delete failed: ' . $e->getMessage());
            return response()->json(['message'=>'Section delete failed', 'op' => 'delete', 'success' => false, 'error' => $e->getMessage()],500);
        }
    }

    public function exportExcel(Request $request)
    {
        $query = Section::with(['course','instructor','room','term']);
        $search = $request->input('search', $request->query('search'));
        if($search) $query->where('section_code','like',"%{$search}%");
        
        // Get filtered records
    $items = $this->getFilteredRecordsForExport($request, $query, Section::class, 'section_code');

        try {
            // Try to use Maatwebsite Excel export
            if (class_exists('\Maatwebsite\Excel\Facades\Excel')) {
                $export = new \App\Exports\SectionExport($items);
                return Excel::download($export, $this->getExportFilename('sections', 'xlsx'));
            }
        } catch (\Throwable $e) {
            \Log::warning('Excel export failed, falling back to CSV: ' . $e->getMessage());
        }

        // Fallback to CSV
        $headers = ['ID','Section Code','Course','Term','Instructor','Room','Max Capacity'];
        
        return $this->downloadCsv('sections', function($file) use ($items, $headers) {
            fputcsv($file, $headers);
            foreach ($items as $i) {
                fputcsv($file, [
                    $i->section_id,
                    $i->section_code,
                    optional($i->course)->course_code,
                    optional($i->term)->term_code,
                    optional($i->instructor)->last_name,
                    optional($i->room)->room_code,
                    $i->max_capacity
                ]);
            }
        }, 'Section Records');
    }

    public function exportPDF(Request $request)
    {
        $query = Section::with(['course','instructor','room','term']);
        $search = $request->input('search', $request->query('search'));
        if($search) $query->where('section_code','like',"%{$search}%");
        $sections = $query->orderBy('section_id','asc')->get();

        // Load PDF view and apply standard footer
        $pdf = Pdf::loadView('sections.export_pdf', compact('sections') + ['logoDataUri' => $this->getLogoDataUri()]);

        // Apply standard footer with page numbers
        $this->applyPdfFooter($pdf);

        // Download with standardized filename
        return $pdf->download($this->getExportFilename('sections', 'pdf'));
    }
}
