<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Section;
use Illuminate\Http\Request;

class GradesController extends Controller
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

        return view('faculty.grades.show', [
            'section' => $section,
            'enrollments' => $enrollments,
        ]);
    }

    public function store(Section $section, Request $request)
    {
        $instructor = Auth::user()->instructor;
        
        if ($section->instructor_id !== $instructor->instructor_id) {
            return redirect('/faculty/sections')->with('error', 'Unauthorized');
        }

        $grades = $request->validate([
            'grades' => 'required|array',
            'grades.*' => 'required|string|max:2',
        ]);

        foreach ($grades['grades'] as $enrollmentId => $grade) {
            \App\Models\Enrollment::where('enrollment_id', $enrollmentId)
                ->where('section_id', $section->section_id)
                ->update(['letter_grade' => $grade]);
        }

        return redirect()->back()->with('success', 'Grades updated successfully');
    }
}
