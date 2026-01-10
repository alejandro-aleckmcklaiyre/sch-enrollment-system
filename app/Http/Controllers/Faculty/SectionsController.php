<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SectionsController extends Controller
{
    public function index()
    {
        $instructor = Auth::user()->instructor;
        
        if (!$instructor) {
            return redirect('/faculty/profile')->with('error', 'Instructor profile not found');
        }

        $sections = \App\Models\Section::where('instructor_id', $instructor->instructor_id)
            ->with(['course', 'term'])
            ->get();

        return view('faculty.sections.index', ['sections' => $sections]);
    }

    public function show(\App\Models\Section $section)
    {
        $instructor = Auth::user()->instructor;
        
        if ($section->instructor_id !== $instructor->instructor_id) {
            return redirect('/faculty/sections')->with('error', 'Unauthorized');
        }

        $section->load(['course', 'term', 'instructor', 'room']);
        
        return view('faculty.sections.show', ['section' => $section]);
    }
}
