<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Section;

class EnrollmentsController extends Controller
{
    public function show(Section $section)
    {
        $instructor = Auth::user()->instructor;
        
        if ($section->instructor_id !== $instructor->instructor_id) {
            return redirect('/faculty/sections')->with('error', 'Unauthorized');
        }

        $enrollments = \App\Models\Enrollment::where('section_id', $section->section_id)
            ->where('is_deleted', 0)
            ->with('student')
            ->get();

        return view('faculty.enrollments.show', [
            'section' => $section,
            'enrollments' => $enrollments,
        ]);
    }
}
