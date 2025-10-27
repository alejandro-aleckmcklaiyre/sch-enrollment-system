<?php

namespace App\Http\Controllers;

use App\Models\Instructor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Traits\HandlesExports;

class InstructorController extends Controller
{
    use HandlesExports;
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

        // Add department filter
        if ($deptId = $request->input('dept_id')) {
            $query->where('dept_id', $deptId);
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
            'email' => [
                'nullable','email',
                \Illuminate\Validation\Rule::unique('tblinstructor','email')->where(function($q){ $q->where('is_deleted',0); }),
            ],
            'dept_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'op' => 'add', 'success' => false], 422);
        }
        // Duplicate check by email (ignore soft-deleted rows)
        if (!empty($data['email']) && Instructor::where('email', $data['email'])->where('is_deleted', 0)->exists()) {
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
            'email' => [
                'nullable','email',
                \Illuminate\Validation\Rule::unique('tblinstructor','email')->ignore($instructor->instructor_id,'instructor_id')->where(function($q){ $q->where('is_deleted',0); }),
            ],
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
            // Make email unique before soft delete to avoid unique constraint error
            $originalEmail = (string) $instructor->email;
            $suffix = '_deleted_' . time();
            $maxLen = 100; // email column is varchar(100)
            if (strlen($originalEmail) + strlen($suffix) > $maxLen) {
                $trunc = substr($originalEmail, 0, $maxLen - strlen($suffix));
            } else {
                $trunc = $originalEmail;
            }
            $instructor->email = $trunc . $suffix;
            $instructor->is_deleted = 1;
            $instructor->save();
            return response()->json(['message' => 'Instructor deleted', 'op' => 'delete', 'success' => true]);
        } catch (\Exception $e) {
            $msg = 'Instructor delete failed';
            if ($e->getMessage()) {
                $msg .= ': ' . $e->getMessage();
            }
            \Log::error($msg);
            return response()->json(['message' => $msg, 'op' => 'delete', 'success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function exportExcel(Request $request)
    {
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

        // enforce ascending export order
        $instructors = $filtered ? $query->orderBy($sortBy,'asc')->get() : Instructor::orderBy($sortBy,'asc')->get();

        try {
            if (class_exists('\Maatwebsite\Excel\Facades\Excel')) {
                $export = new \App\Exports\InstructorExport($instructors);
                return \Maatwebsite\Excel\Facades\Excel::download($export, $this->getExportFilename('instructors', 'xlsx'));
            }
        } catch (\Throwable $e) {
            \Log::warning('Instructor Excel export failed, falling back to CSV: ' . $e->getMessage());
        }

        // Fallback to CSV
        $headers = ['ID','Last Name','First Name','Email','Dept ID'];
        
        return $this->downloadCsv('instructors', function($file) use ($instructors, $headers) {
            fputcsv($file, $headers);
            foreach ($instructors as $i) {
                fputcsv($file, [
                    $i->instructor_id, 
                    $i->last_name, 
                    $i->first_name, 
                    $i->email, 
                    $i->dept_id
                ]);
            }
        }, 'Instructor Records');
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

    // enforce ascending order for PDF export
    $instructors = $filtered ? $query->orderBy('instructor_id','asc')->get() : Instructor::orderBy('instructor_id','asc')->get();

        // Load PDF view with standard settings
        $pdf = Pdf::loadView('instructors.export_pdf', [
            'instructors' => $instructors,
            'logoDataUri' => $this->getLogoDataUri()
        ]);
    
        // Apply standard footer with page numbers
        $this->applyPdfFooter($pdf);
    
        // Generate filename and download
        return $pdf->download($this->getExportFilename('instructors', 'pdf'));
    }
}
