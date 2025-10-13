<?php

namespace App\Http\Controllers;

use App\Models\Instructor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class InstructorController extends Controller
{
    public function index(Request $request)
    {
        $query = Instructor::with('department');

        $allowedSorts = ['instructor_id','last_name','first_name','email','dept_id'];
        $sortBy = $request->input('sort_by', 'instructor_id');
        $sortDir = strtolower($request->input('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'instructor_id';

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('last_name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 15);

    $instructors = $query->orderBy($sortBy, $sortDir)->paginate($perPage)->withQueryString();

        $departments = \App\Models\Department::orderBy('dept_name')->get();

        return view('instructors.index', compact('instructors','departments'));
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'last_name' => 'required|string',
            'first_name' => 'required|string',
            'email' => 'nullable|email|unique:tblinstructor,email',
            'dept_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'op' => 'add', 'success' => false], 422);
        }
        // Duplicate check by email
        if (!empty($data['email']) && Instructor::where('email', $data['email'])->exists()) {
            return response()->json(['message' => 'An instructor with that email already exists in records.', 'op' => 'add', 'success' => false], 409);
        }
        try {
            $instructor = Instructor::create($data);
            \Log::info('Instructor created: ' . $instructor->instructor_id);
            return response()->json(['message' => 'Instructor created', 'op' => 'add', 'success' => true, 'data' => $instructor]);
        } catch (\Exception $e) {
            \Log::error('Instructor create failed: ' . $e->getMessage());
            return response()->json(['message' => 'Instructor create failed', 'op' => 'add', 'success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $instructor = Instructor::findOrFail($id);
        $data = $request->all();

        $validator = Validator::make($data, [
            'last_name' => 'required|string',
            'first_name' => 'required|string',
            'email' => 'nullable|email|unique:tblinstructor,email,' . $instructor->instructor_id . ',instructor_id',
            'dept_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'op' => 'update', 'success' => false], 422);
        }
        try {
            $instructor->update($data);
            return response()->json(['message' => 'Instructor updated', 'op' => 'update', 'success' => true, 'data' => $instructor]);
        } catch (\Exception $e) {
            \Log::error('Instructor update failed: ' . $e->getMessage());
            return response()->json(['message' => 'Instructor update failed', 'op' => 'update', 'success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $instructor = Instructor::findOrFail($id);
        try {
            $instructor->delete();
            return response()->json(['message' => 'Instructor deleted', 'op' => 'delete', 'success' => true]);
        } catch (\Exception $e) {
            \Log::error('Instructor delete failed: ' . $e->getMessage());
            return response()->json(['message' => 'Instructor delete failed', 'op' => 'delete', 'success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function exportExcel(Request $request)
    {
        // For portability use CSV fallback. If you want real Excel, install maatwebsite/excel.
        $filtered = $request->input('filtered', false);
        $search = $request->input('search');
        $dept = $request->input('dept_id');

        // sorting
        $allowedSorts = ['instructor_id','last_name','first_name','email','dept_id'];
        $sortBy = $request->input('sort_by', 'instructor_id');
        $sortDir = strtolower($request->input('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'instructor_id';

        $query = Instructor::query();
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('last_name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        if ($dept) $query->where('dept_id', $dept);

    $instructors = $filtered ? $query->orderBy($sortBy,$sortDir)->get() : Instructor::orderBy($sortBy,$sortDir)->get();

        $filename = 'instructors.csv';
        $headers = ['ID','Last Name','First Name','Email','Dept ID'];

        $callback = function() use ($instructors, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            foreach ($instructors as $i) {
                fputcsv($file, [$i->instructor_id, $i->last_name, $i->first_name, $i->email, $i->dept_id]);
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
        $dept = $request->input('dept_id');

        $query = Instructor::query();
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('last_name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        if ($dept) $query->where('dept_id', $dept);

        $instructors = $filtered ? $query->orderBy('instructor_id','desc')->get() : Instructor::orderBy('instructor_id','desc')->get();

        $pdf = Pdf::loadView('instructors.export_pdf', ['instructors' => $instructors]);

        return $pdf->download('instructors.pdf');
    }
}
