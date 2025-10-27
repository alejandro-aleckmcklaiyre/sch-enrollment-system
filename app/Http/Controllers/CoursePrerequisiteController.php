<?php

namespace App\Http\Controllers;

use App\Models\CoursePrerequisite;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Traits\HandlesExports;

class CoursePrerequisiteController extends Controller
{
    use HandlesExports;
    
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        $query = CoursePrerequisite::with(['course','prereq']);
        if($search = $request->query('search')){
            $query->whereHas('course', function($q) use($search){ $q->where('course_code','like',"%{$search}%"); });
        }
        $allowedSorts = ['course_id','prereq_course_id'];
        $sortBy = $request->input('sort_by', 'course_id');
        $sortDir = strtolower($request->input('sort_dir', 'asc')) === 'asc' ? 'asc' : 'desc';
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'course_id';

        $prereqs = $query->orderBy($sortBy, $sortDir)->paginate($perPage)->withQueryString();
        $courses = Course::orderBy('course_code')->get();
        return view('course_prerequisites.index', compact('prereqs','courses'));
    }

    public function store(Request $request)
    {
        $data = $request->only(['course_id','prereq_course_id']);
        $validator = Validator::make($data, [
            'course_id'=>'required|exists:tblcourse,course_id',
            'prereq_course_id'=>'required|exists:tblcourse,course_id'
        ]);
        if($validator->fails()) return response()->json(['errors'=>$validator->errors(), 'op' => 'add', 'success' => false],422);
        try {
            $pr = CoursePrerequisite::create($data);
            return response()->json(['message'=>'Prerequisite added', 'op' => 'add', 'success' => true, 'data' => $pr]);
        } catch (\Exception $e) {
            \Log::error('Prerequisite create failed: ' . $e->getMessage());
            return response()->json(['message'=>'Prerequisite create failed', 'op' => 'add', 'success' => false, 'error' => $e->getMessage()],500);
        }
    }

    public function destroy($id)
    {
        if(strpos($id,':') !== false){
            [$course,$pre] = explode(':',$id);
            try {
                CoursePrerequisite::where('course_id',$course)
                    ->where('prereq_course_id',$pre)
                    ->delete();
                return response()->json(['message'=>'Prerequisite deleted', 'op' => 'delete', 'success' => true]);
            } catch (\Exception $e) {
                \Log::error('Prerequisite delete failed: ' . $e->getMessage());
                return response()->json(['message'=>'Prerequisite delete failed', 'op' => 'delete', 'success' => false, 'error' => $e->getMessage()],500);
            }
        }
        return response()->json(['error'=>'Invalid id', 'success' => false],400);
    }

    public function update(Request $request, $id)
    {
        if(strpos($id,':') === false){
            return response()->json(['message'=>'Invalid id','success'=>false],400);
        }
        [$course,$pre] = explode(':',$id);
        $data = $request->only(['course_id','prereq_course_id']);
        $validator = Validator::make($data, [
            'course_id'=>'required|exists:tblcourse,course_id',
            'prereq_course_id'=>'required|exists:tblcourse,course_id'
        ]);
        if($validator->fails()) return response()->json(['errors'=>$validator->errors(), 'op' => 'update', 'success' => false],422);
        try{
            $pr = CoursePrerequisite::where('course_id',$course)->where('prereq_course_id',$pre)->firstOrFail();
            $pr->course_id = $data['course_id'];
            $pr->prereq_course_id = $data['prereq_course_id'];
            $pr->save();
            return response()->json(['message'=>'Prerequisite updated', 'op' => 'update', 'success' => true, 'data' => $pr]);
        } catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
            return response()->json(['message'=>'Prerequisite not found','success'=>false],404);
        } catch(\Exception $e){
            \Log::error('Prerequisite update failed: ' . $e->getMessage());
            return response()->json(['message'=>'Prerequisite update failed', 'op' => 'update', 'success' => false, 'error' => $e->getMessage()],500);
        }
    }

    public function exportExcel(Request $request)
    {
        $query = CoursePrerequisite::with(['course','prereq']);
        $search = $request->input('search', $request->query('search'));
        if($search) $query->whereHas('course', function($q) use($search){ $q->where('course_code','like',"%{$search}%"); });
        
        // Get filtered records
        $items = $this->getFilteredRecordsForExport($request, $query, CoursePrerequisite::class, 'course_id');

        try {
            // Try to use Maatwebsite Excel export
            if (class_exists('\Maatwebsite\Excel\Facades\Excel')) {
                $export = new \App\Exports\CoursePrerequisiteExport($items);
                return Excel::download($export, $this->getExportFilename('course_prerequisites', 'xlsx'));
            }
        } catch (\Throwable $e) {
            \Log::warning('Excel export failed, falling back to CSV: ' . $e->getMessage());
        }

        // Fallback to CSV
        $headers = ['Course','Prerequisite'];
        
        return $this->downloadCsv('course_prerequisites', function($file) use ($items, $headers) {
            fputcsv($file, $headers);
            foreach ($items as $i) {
                fputcsv($file, [
                    optional($i->course)->course_code,
                    optional($i->prereq)->course_code
                ]);
            }
        }, 'Course Prerequisites');
    }

    public function exportPDF(Request $request)
    {
        $query = CoursePrerequisite::with(['course','prereq']);
        $search = $request->input('search', $request->query('search'));
        if($search) $query->whereHas('course', function($q) use($search){ $q->where('course_code','like',"%{$search}%"); });
        
        // enforce ascending order for PDF
        $prereqs = $query->orderBy('course_id','asc')->get();

        // Load PDF view and apply standard footer
        $pdf = Pdf::loadView('course_prerequisites.export_pdf', compact('prereqs') + ['logoDataUri' => $this->getLogoDataUri()]);

        // Apply standard footer with page numbers
        $this->applyPdfFooter($pdf);

        // Download with standardized filename
        return $pdf->download($this->getExportFilename('course_prerequisites', 'pdf'));
    }
}