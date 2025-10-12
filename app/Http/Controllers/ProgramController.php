<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class ProgramController extends Controller
{
    public function index(Request $request)
    {
        $query = Program::with('department');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('program_code', 'like', "%{$search}%")
                  ->orWhere('program_name', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 15);

        $programs = $query->orderBy('program_id', 'desc')->paginate($perPage)->withQueryString();

        // load departments for the dropdown
        $departments = \App\Models\Department::orderBy('dept_name')->get();

        return view('programs.index', compact('programs','departments'));
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'program_code' => 'required|string|unique:tblprogram,program_code',
            'program_name' => 'required|string',
            'dept_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Program::create($data);

        return response()->json(['message' => 'Program created']);
    }

    public function update(Request $request, $id)
    {
        $program = Program::findOrFail($id);
        $data = $request->all();

        $validator = Validator::make($data, [
            'program_code' => 'required|string|unique:tblprogram,program_code,' . $program->program_id . ',program_id',
            'program_name' => 'required|string',
            'dept_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $program->update($data);

        return response()->json(['message' => 'Program updated']);
    }

    public function destroy($id)
    {
        $program = Program::findOrFail($id);
        $program->delete();

        return response()->json(['message' => 'Program deleted']);
    }

    // Export to Excel (CSV fallback)
    public function exportExcel(Request $request)
    {
        $filtered = $request->input('filtered', false);
        $search = $request->input('search');

        $query = Program::query();
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('program_code', 'like', "%{$search}%")
                  ->orWhere('program_name', 'like', "%{$search}%");
            });
        }

        $programs = $filtered ? $query->orderBy('program_id','desc')->get() : Program::orderBy('program_id','desc')->get();

        $filename = 'programs.csv';
        $headers = ['ID','Program Code','Program Name','Dept ID'];

        $callback = function() use ($programs, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            foreach ($programs as $p) {
                fputcsv($file, [$p->program_id, $p->program_code, $p->program_name, $p->dept_id]);
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

        $query = Program::query();
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('program_code', 'like', "%{$search}%")
                  ->orWhere('program_name', 'like', "%{$search}%");
            });
        }

        $programs = $filtered ? $query->orderBy('program_id','desc')->get() : Program::orderBy('program_id','desc')->get();

        $pdf = Pdf::loadView('programs.export_pdf', ['programs' => $programs]);

        return $pdf->download('programs.pdf');
    }
}
