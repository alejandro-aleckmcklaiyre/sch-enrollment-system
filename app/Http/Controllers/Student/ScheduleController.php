<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            return redirect('/student/dashboard')->with('error', 'Student profile not found');
        }

        $currentTerm = \App\Models\Term::getCurrentTerm();

        $enrollments = \App\Models\Enrollment::where('student_id', $student->student_id)
            ->where('status', 'ENROLLED')
            ->where('is_deleted', 0)
            ->with(['section.course', 'section.instructor', 'section.room', 'section.term'])
            ->get();

        return view('student.schedule', [
            'enrollments' => $enrollments,
            'currentTerm' => $currentTerm,
            'student' => $student
        ]);
    }
}
