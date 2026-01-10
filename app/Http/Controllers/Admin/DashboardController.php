<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\Course;
use App\Models\Enrollment;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard with system overview
     */
    public function index()
    {
        // Get statistics
        $totalUsers = User::count();
        $totalStudents = Student::where('is_deleted', 0)->count();
        $totalInstructors = Instructor::where('is_deleted', 0)->count();
        $totalCourses = Course::where('is_deleted', 0)->count();
        $totalEnrollments = Enrollment::where('is_deleted', 0)->count();
        
        // Get recent activities
        $recentUsers = User::latest()->take(5)->get();
        $recentEnrollments = Enrollment::with('student', 'section')
            ->where('is_deleted', 0)
            ->orderBy('date_enrolled', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'totalStudents' => $totalStudents,
            'totalInstructors' => $totalInstructors,
            'totalCourses' => $totalCourses,
            'totalEnrollments' => $totalEnrollments,
            'recentUsers' => $recentUsers,
            'recentEnrollments' => $recentEnrollments,
        ]);
    }
}
