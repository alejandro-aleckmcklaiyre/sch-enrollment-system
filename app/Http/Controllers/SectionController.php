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

class SectionController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        $query = Section::with(['course','instructor','room','term']);

        if ($search = $request->query('search')) {
            $query->where('section_code','like',"%{$search}%");
        }
        $sections = $query->orderBy('section_code')->paginate($perPage)->withQueryString();

        $courses = Course::orderBy('course_code')->get();
        $instructors = Instructor::orderBy('last_name')->get();
        $rooms = Room::orderBy('room_code')->get();
        $terms = Term::orderBy('term_code')->get();

        return view('sections.index', compact('sections','courses','instructors','rooms','terms'));
    }

    public function store(Request $request)
    {
        $data = $request->only(['section_code','course_id','term_id','instructor_id','day_pattern','start_time','end_time','room_id','max_capacity']);
        $validator = Validator::make($data, [
            'section_code' => 'required|string|max:20',
            'course_id' => 'required|exists:tblcourse,course_id',
        ]);
        if ($validator->fails()) return response()->json(['errors'=>$validator->errors()],422);
        Section::create($data);
        return response()->json(['message'=>'Section created']);
    }

    public function update(Request $request, $id)
    {
        $s = Section::findOrFail($id);
        $data = $request->only(['section_code','course_id','term_id','instructor_id','day_pattern','start_time','end_time','room_id','max_capacity']);
        $validator = Validator::make($data, [
            'section_code' => 'required|string|max:20',
            'course_id' => 'required|exists:tblcourse,course_id',
        ]);
        if ($validator->fails()) return response()->json(['errors'=>$validator->errors()],422);
        $s->update($data);
        return response()->json(['message'=>'Section updated']);
    }

    public function destroy($id)
    {
        $s = Section::findOrFail($id);
        $s->delete();
        return response()->json(['message'=>'Section deleted']);
    }

    public function exportExcel(Request $request)
    {
        $query = Section::with(['course','instructor','room','term']);
        $search = $request->input('search', $request->query('search'));
        if($search) $query->where('section_code','like',"%{$search}%");
        $items = $query->orderBy('section_code')->get();

        $filename = 'sections_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($items) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Section Code','Course','Term','Instructor','Room','Max Capacity']);
            foreach ($items as $i) {
                fputcsv($out, [
                    $i->section_code,
                    optional($i->course)->course_code,
                    optional($i->term)->term_code,
                    optional($i->instructor)->last_name,
                    optional($i->room)->room_code,
                    $i->max_capacity,
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPDF(Request $request)
    {
        $query = Section::with(['course','instructor','room','term']);
        $search = $request->input('search', $request->query('search'));
        if($search) $query->where('section_code','like',"%{$search}%");
        $sections = $query->orderBy('section_code')->get();
        $pdf = Pdf::loadView('sections.export_pdf', compact('sections'));
        return $pdf->download('sections.pdf');
    }
}
