<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Traits\HandlesExports;

class RoomController extends Controller
{
    use HandlesExports;
    public function index(Request $request)
    {
        $query = Room::query();

        $allowedSorts = ['room_id','room_code','building','capacity'];
        $sortBy = $request->input('sort_by', 'room_id');
        $sortDir = strtolower($request->input('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'room_id';

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('room_code', 'like', "%{$search}%")
                  ->orWhere('building', 'like', "%{$search}%");
            });
        }

        // Add building filter
        if ($building = $request->input('building')) {
            $query->where('building', $building);
        }

        $perPage = $request->input('per_page', 15);

        // Get unique buildings for filter dropdown
        $buildings = Room::where('is_deleted', 0)
            ->whereNotNull('building')
            ->distinct()
            ->orderBy('building')
            ->pluck('building');

        $rooms = $query->orderBy($sortBy, $sortDir)->paginate($perPage)->withQueryString();

        return view('rooms.index', compact('rooms', 'buildings'));
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'building' => 'nullable|string',
            'room_code' => [
                'required','string',
                \Illuminate\Validation\Rule::unique('tblroom','room_code')->where(function($q){ $q->where('is_deleted',0); }),
            ],
            'capacity' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'op' => 'add', 'success' => false], 422);
        }
        // Duplicate check by room_code (ignore soft-deleted rows)
        if (!empty($data['room_code']) && Room::where('room_code', $data['room_code'])->where('is_deleted', 0)->exists()) {
            return response()->json(['message' => 'A room with that code already exists in records.', 'op' => 'add', 'success' => false], 409);
        }
        try {
            $room = Room::create($data);
            \Log::info('Room created: ' . $room->room_id);
            return response()->json(['message' => 'Room created', 'op' => 'add', 'success' => true, 'data' => $room]);
        } catch (\Exception $e) {
            \Log::error('Room create failed: ' . $e->getMessage());
            return response()->json(['message' => 'Room create failed', 'op' => 'add', 'success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $room = Room::findOrFail($id);
        $data = $request->all();

        $validator = Validator::make($data, [
            'building' => 'nullable|string',
            'room_code' => [
                'required','string',
                \Illuminate\Validation\Rule::unique('tblroom','room_code')->ignore($room->room_id,'room_id')->where(function($q){ $q->where('is_deleted',0); }),
            ],
            'capacity' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'op' => 'update', 'success' => false], 422);
        }
        try {
            $room->update($data);
            return response()->json(['message' => 'Room updated', 'op' => 'update', 'success' => true, 'data' => $room]);
        } catch (\Exception $e) {
            \Log::error('Room update failed: ' . $e->getMessage());
            return response()->json(['message' => 'Room update failed', 'op' => 'update', 'success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $room = Room::findOrFail($id);
        try {
            $room->delete();
            return response()->json(['message' => 'Room deleted', 'op' => 'delete', 'success' => true]);
        } catch (\Exception $e) {
            \Log::error('Room delete failed: ' . $e->getMessage());
            return response()->json(['message' => 'Room delete failed', 'op' => 'delete', 'success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function exportExcel(Request $request)
    {
        $filtered = $request->input('filtered', false);
        $search = $request->input('search');

        // sorting
        $allowedSorts = ['room_id','room_code','building','capacity'];
        $sortBy = $request->input('sort_by', 'room_id');
        $sortDir = strtolower($request->input('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'room_id';

        $query = Room::query();
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('room_code', 'like', "%{$search}%")
                  ->orWhere('building', 'like', "%{$search}%");
            });
        }

    // enforce ascending export order
    $rooms = $filtered ? $query->orderBy($sortBy,'asc')->get() : Room::orderBy($sortBy,'asc')->get();
        try {
            if (class_exists('\Maatwebsite\Excel\Facades\Excel')) {
                $export = new \App\Exports\RoomExport($rooms);
                return \Maatwebsite\Excel\Facades\Excel::download($export, $this->getExportFilename('rooms', 'xlsx'));
            }
        } catch (\Throwable $e) {
            \Log::warning('Room Excel export failed, falling back to CSV: ' . $e->getMessage());
        }

        // Fallback to CSV
        $headers = ['ID','Building','Room Code','Capacity'];
        
        return $this->downloadCsv('rooms', function($file) use ($rooms, $headers) {
            fputcsv($file, $headers);
            foreach ($rooms as $r) {
                fputcsv($file, [
                    $r->room_id,
                    $r->building,
                    $r->room_code,
                    $r->capacity
                ]);
            }
        }, 'Room Records');
    }

    public function exportPDF(Request $request)
    {
        $filtered = $request->input('filtered', false);
        $search = $request->input('search');

        $query = Room::query();
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('room_code', 'like', "%{$search}%")
                  ->orWhere('building', 'like', "%{$search}%");
            });
        }

        $rooms = $filtered ? $query->orderBy('room_id', 'asc')->get() : Room::orderBy('room_id', 'asc')->get();

        // Load PDF view and apply standard footer
        $pdf = Pdf::loadView('rooms.export_pdf', [
            'rooms' => $rooms,
            'logoDataUri' => $this->getLogoDataUri()
        ]);

        // Apply standard footer with page numbers
        $this->applyPdfFooter($pdf);

        // Download with standardized filename
        return $pdf->download($this->getExportFilename('rooms', 'pdf'));
    }
}
