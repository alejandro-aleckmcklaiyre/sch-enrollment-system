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

        if ($search = $request->query('search')) {
            $query->where('dept_name', 'like', "%{$search}%")
                  ->orWhere('dept_code', 'like', "%{$search}%");
        }

        $departments = $query->orderBy('dept_name')->paginate($perPage)->withQueryString();

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
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Department::create($data);

        return response()->json(['message' => 'Department created']);
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
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $dept->update($data);

        return response()->json(['message' => 'Department updated']);
    }

    public function destroy($id)
    {
        $dept = Department::findOrFail($id);
        $dept->delete();

        return response()->json(['message' => 'Department deleted']);
    }

    public function exportExcel(Request $request)
    {
        $query = Department::query();
        if ($search = $request->query('search')) {
            $query->where('dept_name', 'like', "%{$search}%")
                  ->orWhere('dept_code', 'like', "%{$search}%");
        }

        $items = $query->orderBy('dept_name')->get();

        $filename = 'departments_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($items) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Dept Code', 'Dept Name']);
            foreach ($items as $i) {
                fputcsv($out, [$i->dept_code, $i->dept_name]);
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

        $items = $query->orderBy('dept_name')->get();

        $pdf = Pdf::loadView('departments.export_pdf', compact('items'));
        return $pdf->download('departments.pdf');
    }
}
