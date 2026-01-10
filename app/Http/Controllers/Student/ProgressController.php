<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
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
            ->get();

        $totalEnrolled = $allEnrollments->count();
        $completed = $allEnrollments->where('status', 'COMPLETED')->count();
        $enrolled = $allEnrollments->where('status', 'ENROLLED')->count();
        $dropped = $allEnrollments->where('status', 'DROPPED')->count();

        // Calculate progress percentage
        $progressPercentage = $totalEnrolled > 0 ? round(($completed / $totalEnrolled) * 100) : 0;

        // Get current term enrollments
        $currentTerm = \App\Models\Term::getCurrentTerm();
        $currentTermEnrollments = $allEnrollments->filter(function($e) use ($currentTerm) {
            return $currentTerm && $e->section->term_id == $currentTerm->term_id;
        });

        return view('student.progress', [
            'student' => $student,
            'totalEnrolled' => $totalEnrolled,
            'completed' => $completed,
            'enrolled' => $enrolled,
            'dropped' => $dropped,
            'progressPercentage' => $progressPercentage,
            'currentTermEnrollments' => $currentTermEnrollments,
            'allEnrollments' => $allEnrollments,
        ]);
    }
}
