<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TranscriptController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            return redirect('/student/dashboard')->with('error', 'Student profile not found');
        }

        $allEnrollments = \App\Models\Enrollment::where('student_id', $student->student_id)
            ->where('is_deleted', 0)
            ->with(['section.course', 'section.term'])
            ->orderBy('date_enrolled', 'desc')
            ->get();

        // Group by term
        $enrollmentsByTerm = $allEnrollments->groupBy('section.term_id');

        // Calculate stats
        $completedCourses = $allEnrollments->where('status', 'COMPLETED')->count();
        $totalCredits = $allEnrollments->where('status', 'COMPLETED')
            ->sum(function($e) { return $e->section->course->credit_units ?? 0; });

        return view('student.transcript', [
            'enrollments' => $allEnrollments,
            'enrollmentsByTerm' => $enrollmentsByTerm,
            'completedCourses' => $completedCourses,
            'totalCredits' => $totalCredits,
            'student' => $student
        ]);
    }

    public function download()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            return redirect('/student/dashboard')->with('error', 'Student profile not found');
        }

        return redirect('/student/transcript')->with('info', 'Download functionality coming soon');
    }
}
