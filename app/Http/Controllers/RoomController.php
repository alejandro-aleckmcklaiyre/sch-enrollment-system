<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('room_code', 'like', "%{$search}%")
                  ->orWhere('building', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 15);

        $rooms = $query->orderBy('room_id', 'desc')->paginate($perPage)->withQueryString();

        return view('rooms.index', compact('rooms'));
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'building' => 'nullable|string',
            'room_code' => 'required|string|unique:tblroom,room_code',
            'capacity' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Room::create($data);

        return response()->json(['message' => 'Room created']);
    }

    public function update(Request $request, $id)
    {
        $room = Room::findOrFail($id);
        $data = $request->all();

        $validator = Validator::make($data, [
            'building' => 'nullable|string',
            'room_code' => 'required|string|unique:tblroom,room_code,' . $room->room_id . ',room_id',
            'capacity' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $room->update($data);

        return response()->json(['message' => 'Room updated']);
    }

    public function destroy($id)
    {
        $room = Room::findOrFail($id);
        $room->delete();

        return response()->json(['message' => 'Room deleted']);
    }

    public function exportExcel(Request $request)
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

        $rooms = $filtered ? $query->orderBy('room_id','desc')->get() : Room::orderBy('room_id','desc')->get();

        $filename = 'rooms.csv';
        $headers = ['ID','Building','Room Code','Capacity'];

        $callback = function() use ($rooms, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            foreach ($rooms as $r) {
                fputcsv($file, [$r->room_id, $r->building, $r->room_code, $r->capacity]);
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

        $query = Room::query();
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('room_code', 'like', "%{$search}%")
                  ->orWhere('building', 'like', "%{$search}%");
            });
        }

        $rooms = $filtered ? $query->orderBy('room_id','desc')->get() : Room::orderBy('room_id','desc')->get();

        $pdf = Pdf::loadView('rooms.export_pdf', ['rooms' => $rooms]);

        return $pdf->download('rooms.pdf');
    }
}
