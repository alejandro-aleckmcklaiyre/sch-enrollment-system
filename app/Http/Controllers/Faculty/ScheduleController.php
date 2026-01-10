<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function index()
    {
        $instructor = Auth::user()->instructor;
        
        if (!$instructor) {
            return redirect('/faculty/profile')->with('error', 'Instructor profile not found');
        }

        $sections = \App\Models\Section::where('instructor_id', $instructor->instructor_id)
            ->with(['course', 'term', 'room'])
            ->get();

        return view('faculty.schedule', ['sections' => $sections]);
    }
}
