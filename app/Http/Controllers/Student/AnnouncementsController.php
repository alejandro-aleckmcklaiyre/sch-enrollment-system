<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AnnouncementsController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            return redirect('/student/dashboard')->with('error', 'Student profile not found');
        }

        // For now, show mock announcements
        // In a full implementation, this would query a proper announcements table
        $announcements = [
            [
                'id' => 1,
                'title' => 'Welcome to Student Portal',
                'body' => 'Welcome to the enrollment system. You can view your courses, schedule, and academic progress here.',
                'date' => now()->subDays(2),
                'type' => 'system'
            ],
            [
                'id' => 2,
                'title' => 'Enrollment Period Open',
                'body' => 'The enrollment period for the next term is now open. You can browse courses in the Course Catalog and enroll in available sections.',
                'date' => now()->subDays(1),
                'type' => 'academic'
            ],
        ];

        return view('student.announcements', [
            'announcements' => $announcements,
            'student' => $student
        ]);
    }
}
