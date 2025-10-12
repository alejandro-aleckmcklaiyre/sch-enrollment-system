<?php

namespace App\Http\Controllers;

use App\Models\CoursePrerequisite;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class CoursePrerequisiteController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        $query = CoursePrerequisite::with(['course','prereq']);
        if($search = $request->query('search')){
            $query->whereHas('course', function($q) use($search){ $q->where('course_code','like',"%{$search}%"); });
        }
    $prereqs = $query->paginate($perPage)->withQueryString();
    $courses = Course::orderBy('course_code')->get();
    return view('course_prerequisites.index', compact('prereqs','courses'));
    }

    public function store(Request $request)
    {
        $data = $request->only(['course_id','prereq_course_id']);
        $validator = Validator::make($data, ['course_id'=>'required|exists:tblcourse,course_id','prereq_course_id'=>'required|exists:tblcourse,course_id']);
        if($validator->fails()) return response()->json(['errors'=>$validator->errors()],422);
        CoursePrerequisite::create($data);
        return response()->json(['message'=>'Prerequisite added']);
    }

    public function destroy($id)
    {
        // composite pk deletion - $id will be passed as course_id:prereq_id
        if(strpos($id,':') !== false){
            [$course,$pre] = explode(':',$id);
            CoursePrerequisite::where('course_id',$course)->where('prereq_course_id',$pre)->delete();
            return response()->json(['message'=>'Prerequisite deleted']);
        }
        return response()->json(['error'=>'Invalid id'],400);
    }

    public function exportExcel(Request $request)
    {
        $query = CoursePrerequisite::with(['course','prereq']);
        $search = $request->input('search', $request->query('search'));
        if($search) $query->whereHas('course', function($q) use($search){ $q->where('course_code','like',"%{$search}%"); });
        $items = $query->get();

        $filename = 'course_prerequisites_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($items) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Course','Prerequisite']);
            foreach ($items as $i) {
                fputcsv($out, [optional($i->course)->course_code, optional($i->prereq)->course_code]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPDF(Request $request)
    {
        $query = CoursePrerequisite::with(['course','prereq']);
        $search = $request->input('search', $request->query('search'));
        if($search) $query->whereHas('course', function($q) use($search){ $q->where('course_code','like',"%{$search}%"); });
        $prereqs = $query->get();
        $pdf = Pdf::loadView('course_prerequisites.export_pdf', compact('prereqs'));
        return $pdf->download('course_prerequisites.pdf');
    }
}
