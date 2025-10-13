<?php

namespace App\Http\Controllers;

use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class TermController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        $query = Term::query();
        if($search = $request->query('search')) $query->where('term_code','like',"%{$search}%");

        $allowedSorts = ['term_id','term_code','start_date','end_date'];
        $sortBy = $request->input('sort_by', 'term_code');
        $sortDir = strtolower($request->input('sort_dir', 'asc')) === 'asc' ? 'asc' : 'desc';
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'term_code';

    $terms = $query->orderBy($sortBy, $sortDir)->paginate($perPage)->withQueryString();
        return view('terms.index', compact('terms'));
    }

    public function store(Request $request)
    {
        $data = $request->only(['term_code','start_date','end_date']);
        $validator = Validator::make($data, ['term_code'=>'required','start_date'=>'required|date','end_date'=>'required|date']);
        if($validator->fails()) return response()->json(['errors'=>$validator->errors(), 'op' => 'add', 'success' => false],422);
        try {
            $t = Term::create($data);
            return response()->json(['message'=>'Term created', 'op' => 'add', 'success' => true, 'data' => $t]);
        } catch (\Exception $e) {
            \Log::error('Term create failed: ' . $e->getMessage());
            return response()->json(['message'=>'Term create failed', 'op' => 'add', 'success' => false, 'error' => $e->getMessage()],500);
        }
    }

    public function update(Request $request, $id)
    {
        $t = Term::findOrFail($id);
        $data = $request->only(['term_code','start_date','end_date']);
        $validator = Validator::make($data, ['term_code'=>'required','start_date'=>'required|date','end_date'=>'required|date']);
        if($validator->fails()) return response()->json(['errors'=>$validator->errors(), 'op' => 'update', 'success' => false],422);
        try {
            $t->update($data);
            return response()->json(['message'=>'Term updated', 'op' => 'update', 'success' => true, 'data' => $t]);
        } catch (\Exception $e) {
            \Log::error('Term update failed: ' . $e->getMessage());
            return response()->json(['message'=>'Term update failed', 'op' => 'update', 'success' => false, 'error' => $e->getMessage()],500);
        }
    }

    public function destroy($id)
    {
        $t = Term::findOrFail($id);
        try {
            $t->delete();
            return response()->json(['message'=>'Term deleted', 'op' => 'delete', 'success' => true]);
        } catch (\Exception $e) {
            \Log::error('Term delete failed: ' . $e->getMessage());
            return response()->json(['message'=>'Term delete failed', 'op' => 'delete', 'success' => false, 'error' => $e->getMessage()],500);
        }
    }

    public function exportExcel(Request $request)
    {
        $query = Term::query();
        $search = $request->input('search', $request->query('search'));
        if($search) $query->where('term_code','like',"%{$search}%");
        $items = $query->orderBy('term_code')->get();

        $filename = 'terms_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($items) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Term Code','Start Date','End Date']);
            foreach ($items as $i) {
                fputcsv($out, [$i->term_code, $i->start_date, $i->end_date]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPDF(Request $request)
    {
        $query = Term::query();
        $search = $request->input('search', $request->query('search'));
        if($search) $query->where('term_code','like',"%{$search}%");
        $terms = $query->orderBy('term_code')->get();
        $pdf = Pdf::loadView('terms.export_pdf', compact('terms'));
        return $pdf->download('terms.pdf');
    }
}
