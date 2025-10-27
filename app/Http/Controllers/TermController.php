<?php

namespace App\Http\Controllers;

use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Traits\HandlesExports;

class TermController extends Controller
{
    use HandlesExports;
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
        
        // Get filtered records
        $items = $this->getFilteredRecordsForExport($request, $query, Term::class, 'term_code');

        try {
            // Try to use Maatwebsite Excel export
            if (class_exists('\Maatwebsite\Excel\Facades\Excel')) {
                $export = new \App\Exports\TermExport($items);
                return Excel::download($export, $this->getExportFilename('terms', 'xlsx'));
            }
        } catch (\Throwable $e) {
            \Log::warning('Excel export failed, falling back to CSV: ' . $e->getMessage());
        }

        // Fallback to CSV
        $headers = ['Term Code','Start Date','End Date'];
        
        return $this->downloadCsv('terms', function($file) use ($items, $headers) {
            fputcsv($file, $headers);
            foreach ($items as $i) {
                fputcsv($file, [
                    $i->term_code, 
                    $i->start_date, 
                    $i->end_date
                ]);
            }
        }, 'Term Records');
    }

    public function exportPDF(Request $request)
    {
        $query = Term::query();
        $search = $request->input('search', $request->query('search'));
        if($search) $query->where('term_code','like',"%{$search}%");
        $terms = $query->orderBy('term_code')->get();
    // prepare logo
    $logoDataUri = null;
    $logoPath = public_path('images/pup_logo.jpg');
    if (file_exists($logoPath)) {
        try {
            if (@getimagesize($logoPath) !== false && filesize($logoPath) > 512) {
                $mime = mime_content_type($logoPath) ?: 'image/jpeg';
                $logoDataUri = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
            } else {
                \Log::warning('terms.exportPDF: logo invalid or too small: ' . $logoPath);
            }
        } catch (\Throwable $ex) {
            \Log::warning('terms.exportPDF: failed to build logo data uri: ' . $ex->getMessage());
        }
    }

    // Load PDF view and apply standard footer
    $pdf = Pdf::loadView('terms.export_pdf', compact('terms') + ['logoDataUri' => $this->getLogoDataUri()]);

    // Apply standard footer with page numbers
    $this->applyPdfFooter($pdf);

    // Download with standardized filename
    return $pdf->download($this->getExportFilename('terms', 'pdf'));
    }
}
