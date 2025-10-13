<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        $query = Department::query();

        $allowedSorts = ['dept_id','dept_name','dept_code'];
        $sortBy = $request->input('sort_by', 'dept_name');
        $sortDir = strtolower($request->input('sort_dir', 'asc')) === 'asc' ? 'asc' : 'desc';
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'dept_name';

        if ($search = $request->query('search')) {
            $query->where('dept_name', 'like', "%{$search}%")
                  ->orWhere('dept_code', 'like', "%{$search}%");
        }

    $departments = $query->orderBy($sortBy, $sortDir)->paginate($perPage)->withQueryString();

        return view('departments.index', compact('departments'));
    }

    public function store(Request $request)
    {
        $data = $request->only(['dept_code','dept_name']);

        $validator = Validator::make($data, [
            'dept_code' => 'required|string|max:20|unique:tbldepartment,dept_code',
            'dept_name' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'op' => 'add', 'success' => false], 422);
        }
        // Duplicate check by dept_code
        if (!empty($data['dept_code']) && Department::where('dept_code', $data['dept_code'])->exists()) {
            return response()->json(['message' => 'A department with that code already exists in records.', 'op' => 'add', 'success' => false], 409);
        }
        try {
            $dept = Department::create($data);
            \Log::info('Department created: ' . $dept->dept_id);
            return response()->json(['message' => 'Department created', 'op' => 'add', 'success' => true, 'data' => $dept]);
        } catch (\Exception $e) {
            \Log::error('Department create failed: ' . $e->getMessage());
            return response()->json(['message' => 'Department create failed', 'op' => 'add', 'success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $dept = Department::findOrFail($id);
        $data = $request->only(['dept_code','dept_name']);

        $validator = Validator::make($data, [
            'dept_code' => 'required|string|max:20|unique:tbldepartment,dept_code,' . $dept->dept_id . ',dept_id',
            'dept_name' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'op' => 'update', 'success' => false], 422);
        }
        try {
            $dept->update($data);
            return response()->json(['message' => 'Department updated', 'op' => 'update', 'success' => true, 'data' => $dept]);
        } catch (\Exception $e) {
            \Log::error('Department update failed: ' . $e->getMessage());
            return response()->json(['message' => 'Department update failed', 'op' => 'update', 'success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $dept = Department::findOrFail($id);
        try {
            $dept->delete();
            return response()->json(['message' => 'Department deleted', 'op' => 'delete', 'success' => true]);
        } catch (\Exception $e) {
            \Log::error('Department delete failed: ' . $e->getMessage());
            return response()->json(['message' => 'Department delete failed', 'op' => 'delete', 'success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function exportExcel(Request $request)
    {
        $query = Department::query();
        if ($search = $request->query('search')) {
            $query->where('dept_name', 'like', "%{$search}%")
                  ->orWhere('dept_code', 'like', "%{$search}%");
        }

        $allowedSorts = ['dept_id','dept_name','dept_code'];
        $sortBy = $request->input('sort_by', 'dept_name');
        $sortDir = strtolower($request->input('sort_dir', 'asc')) === 'asc' ? 'asc' : 'desc';
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'dept_name';

        $items = $query->orderBy($sortBy, $sortDir)->get();

        $filename = 'departments_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($items) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID','Dept Code', 'Dept Name']);
            foreach ($items as $i) {
                fputcsv($out, [$i->dept_id, $i->dept_code, $i->dept_name]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPDF(Request $request)
    {
        $query = Department::query();
        if ($search = $request->query('search')) {
            $query->where('dept_name', 'like', "%{$search}%")
                  ->orWhere('dept_code', 'like', "%{$search}%");
        }

        $allowedSorts = ['dept_id','dept_name','dept_code'];
        $sortBy = $request->input('sort_by', 'dept_name');
        $sortDir = strtolower($request->input('sort_dir', 'asc')) === 'asc' ? 'asc' : 'desc';
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'dept_name';

        $items = $query->orderBy($sortBy, $sortDir)->get();

        $pdf = Pdf::loadView('departments.export_pdf', compact('items'));
        return $pdf->download('departments.pdf');
    }
}
