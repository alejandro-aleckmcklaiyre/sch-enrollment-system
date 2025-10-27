<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Traits\HandlesExports;
use Maatwebsite\Excel\Facades\Excel;

class ProgramController extends Controller
{
    use HandlesExports;
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
            'program_code' => [
                'required','string',
                \Illuminate\Validation\Rule::unique('tblprogram','program_code')->where(function($q){ $q->where('is_deleted',0); }),
            ],
            'program_name' => 'required|string',
            'dept_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'op' => 'add', 'success' => false], 422);
        }

        // Duplicate check by program_code (ignore soft-deleted rows)
        if (!empty($data['program_code']) && Program::where('program_code', $data['program_code'])->where('is_deleted', 0)->exists()) {
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
            'program_code' => [
                'required','string',
                \Illuminate\Validation\Rule::unique('tblprogram','program_code')->ignore($program->program_id,'program_id')->where(function($q){ $q->where('is_deleted',0); }),
            ],
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

    public function exportExcel(Request $request)
    {
        $query = Program::query();
        
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('program_code', 'like', "%{$search}%")
                  ->orWhere('program_name', 'like', "%{$search}%");
            });
        }

        // Get filtered & ordered items
        $items = $this->getFilteredRecordsForExport($request, $query, Program::class);

        try {
            // Try to use Maatwebsite Excel export
            if (class_exists('\Maatwebsite\Excel\Facades\Excel')) {
                $export = new \App\Exports\ProgramsExport($items);
                return Excel::download($export, $this->getExportFilename('programs', 'xlsx'));
            }
        } catch (\Throwable $e) {
            \Log::warning('Excel export failed, falling back to CSV: ' . $e->getMessage());
        }

        // Fallback to CSV
        $headers = ['ID', 'Program Code', 'Program Name', 'Dept ID'];
        
        return $this->downloadCsv('programs', function($file) use ($items, $headers) {
            fputcsv($file, $headers);
            foreach ($items as $item) {
                fputcsv($file, [
                    $item->program_id,
                    $item->program_code,
                    $item->program_name,
                    $item->dept_id
                ]);
            }
        }, 'Program Records');
    }

    public function exportPDF(Request $request)
    {
        $query = Program::with('department');
        
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('program_code', 'like', "%{$search}%")
                  ->orWhere('program_name', 'like', "%{$search}%");
            });
        }

        // Get filtered & ordered items
        $items = $this->getFilteredRecordsForExport($request, $query, Program::class);

        $data = [
            'records' => $items,
            'filtered' => $request->input('filtered', false),
            'search' => $request->input('search'),
            'sort_by' => $request->input('sort_by', 'program_id'),
            'sort_dir' => $request->input('sort_dir', 'asc'),
            'totals' => ['count' => $items->count()],
            'logoDataUri' => $this->getLogoDataUri()  // Changed from 'logo' to 'logoDataUri' to match student page
        ];

        $pdf = PDF::loadView('programs.export_pdf', $data);  // Changed to programs.export_pdf
        $this->applyPdfFooter($pdf);

        return $pdf->download($this->getExportFilename('programs', 'pdf'));
    }
}
