<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class ProgramController extends Controller
{
    public function index(Request $request)
    {
        $query = Program::with('department');

        // sorting
        $allowedSorts = ['program_id','program_code','program_name','dept_id'];
        $sortBy = $request->input('sort_by', 'program_id');
        $sortDir = strtolower($request->input('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'program_id';

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('program_code', 'like', "%{$search}%")
                  ->orWhere('program_name', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 15);

    $programs = $query->orderBy($sortBy, $sortDir)->paginate($perPage)->withQueryString();

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
            return response()->json(['errors' => $validator->errors(), 'op' => 'add', 'success' => false], 422);
        }

        // Duplicate check by program_code
        if (!empty($data['program_code']) && Program::where('program_code', $data['program_code'])->exists()) {
            return response()->json(['message' => 'A program with that code already exists in records.', 'op' => 'add', 'success' => false], 409);
        }

        try {
            $program = Program::create($data);
            \Log::info('Program created: ' . $program->program_id);
            return response()->json(['message' => 'Program created', 'op' => 'add', 'success' => true, 'data' => $program]);
        } catch (\Exception $e) {
            \Log::error('Program create failed: ' . $e->getMessage());
            return response()->json(['message' => 'Program create failed', 'op' => 'add', 'success' => false, 'error' => $e->getMessage()], 500);
        }
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
            return response()->json(['errors' => $validator->errors(), 'op' => 'update', 'success' => false], 422);
        }

        try {
            $program->update($data);
            \Log::info('Program updated: ' . $program->program_id);
            return response()->json(['message' => 'Program updated', 'op' => 'update', 'success' => true, 'data' => $program]);
        } catch (\Exception $e) {
            \Log::error('Program update failed: ' . $e->getMessage());
            return response()->json(['message' => 'Program update failed', 'op' => 'update', 'success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $program = Program::findOrFail($id);
        try {
            $program->delete();
            \Log::info('Program deleted: ' . $program->program_id);
            return response()->json(['message' => 'Program deleted', 'op' => 'delete', 'success' => true]);
        } catch (\Exception $e) {
            \Log::error('Program delete failed: ' . $e->getMessage());
            return response()->json(['message' => 'Program delete failed', 'op' => 'delete', 'success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // Export to Excel (CSV fallback)
    public function exportExcel(Request $request)
    {
        $filtered = $request->input('filtered', false);
        $search = $request->input('search');

        // sorting (mirror index)
        $allowedSorts = ['program_id','program_code','program_name','dept_id'];
        $sortBy = $request->input('sort_by', 'program_id');
        $sortDir = strtolower($request->input('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'program_id';

        $query = Program::query();
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('program_code', 'like', "%{$search}%")
                  ->orWhere('program_name', 'like', "%{$search}%");
            });
        }

    $programs = $filtered ? $query->orderBy($sortBy,$sortDir)->get() : Program::orderBy($sortBy,$sortDir)->get();

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
