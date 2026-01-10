<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use App\Models\User;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Section;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\Room;
use App\Models\Term;
use App\Models\Program;

$app = require_once 'bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

echo "=== STUDENT DATA DIAGNOSTIC ===\n\n";

// Check specific student user
$user = User::where('email', 'student@test.com')->first();
if (!$user) {
    echo "ERROR: student@test.com user not found!\n";
    exit(1);
}

echo "User found: {$user->name} (ID: {$user->id})\n";

// Check if user has student record
$student = $user->student;
echo "Student relationship result: " . (is_null($student) ? 'NULL' : 'NOT NULL') . "\n";

if (!$student) {
    echo "ERROR: User has no linked student record!\n";
    // Try to find student by user_id directly
    $directStudent = \App\Models\Student::where('user_id', $user->id)->first();
    echo "Direct query result: " . (is_null($directStudent) ? 'NULL' : 'NOT NULL') . "\n";
    if ($directStudent) {
        echo "Direct student: {$directStudent->first_name} {$directStudent->last_name} (ID: {$directStudent->student_id})\n";
        $student = $directStudent;
    } else {
        exit(1);
    }
}

echo "Student record found: {$student->first_name} {$student->last_name} (ID: {$student->student_id})\n";
echo "Student program: " . ($student->program ? $student->program->name : 'NULL') . "\n\n";

// Check enrollments
$enrollments = $student->enrollments;
echo "Total enrollments: " . $enrollments->count() . "\n";

if ($enrollments->count() > 0) {
    echo "\n=== ENROLLMENT DETAILS ===\n";
    foreach ($enrollments as $enrollment) {
        echo "\nEnrollment ID: {$enrollment->id}\n";
        echo "Status: {$enrollment->status}\n";
        echo "Section ID: {$enrollment->section_id}\n";

        if ($enrollment->section) {
            $section = $enrollment->section;
            echo "Section: {$section->section_name}\n";
            echo "Course: " . ($section->course ? $section->course->course_name : 'NULL') . "\n";
            echo "Instructor: " . ($section->instructor ? $section->instructor->first_name . ' ' . $section->instructor->last_name : 'NULL') . "\n";
            echo "Room: " . ($section->room ? $section->room->room_name : 'NULL') . "\n";
            echo "Term: " . ($section->term ? $section->term->term_name : 'NULL') . "\n";
        } else {
            echo "ERROR: Section not found!\n";
        }
    }
} else {
    echo "No enrollments found!\n";
}

// Check current term
$currentTerm = Term::getCurrentTerm();
echo "\n=== CURRENT TERM ===\n";
if ($currentTerm) {
    echo "Current term: {$currentTerm->term_name} (ID: {$currentTerm->id})\n";
} else {
    echo "No current term found!\n";
}

// Check current term enrollments
if ($currentTerm) {
    $currentEnrollments = $student->enrollments()->whereHas('section', function($q) use ($currentTerm) {
        $q->where('term_id', $currentTerm->id);
    })->with(['section.course', 'section.instructor', 'section.room', 'section.term'])->get();

    echo "\n=== CURRENT TERM ENROLLMENTS ===\n";
    echo "Count: " . $currentEnrollments->count() . "\n";

    foreach ($currentEnrollments as $enrollment) {
        echo "\n- Course: " . ($enrollment->section->course ? $enrollment->section->course->course_name : 'NULL') . "\n";
        echo "  Instructor: " . ($enrollment->section->instructor ? $enrollment->section->instructor->first_name . ' ' . $enrollment->section->instructor->last_name : 'NULL') . "\n";
        echo "  Room: " . ($enrollment->section->room ? $enrollment->section->room->room_name : 'NULL') . "\n";
        echo "  Term: " . ($enrollment->section->term ? $enrollment->section->term->term_name : 'NULL') . "\n";
    }
}

echo "\n=== CONTROLLER SIMULATION ===\n";

// Simulate what DashboardController does
$enrollmentStats = [
    'total' => $student->enrollments()->count(),
    'current' => $student->enrollments()->whereHas('section', function($q) use ($currentTerm) {
        $q->where('term_id', $currentTerm->id);
    })->count(),
    'completed' => $student->enrollments()->where('status', 'completed')->count(),
    'pending' => $student->enrollments()->where('status', 'pending')->count(),
];

echo "Enrollment Stats:\n";
echo "- Total: {$enrollmentStats['total']}\n";
echo "- Current: {$enrollmentStats['current']}\n";
echo "- Completed: {$enrollmentStats['completed']}\n";
echo "- Pending: {$enrollmentStats['pending']}\n";

echo "\n=== VIEW DATA CHECK ===\n";

// Check what data would be passed to views
$enrollmentsForView = $student->enrollments()
    ->with(['section.course', 'section.instructor', 'section.room', 'section.term'])
    ->get();

echo "Enrollments for view count: " . $enrollmentsForView->count() . "\n";

if ($enrollmentsForView->count() > 0) {
    echo "First enrollment details:\n";
    $first = $enrollmentsForView->first();
    echo "- Course: " . ($first->section?->course?->course_name ?? 'NULL') . "\n";
    echo "  Instructor: " . ($first->section?->instructor?->first_name ?? 'NULL') . " " . ($first->section?->instructor?->last_name ?? 'NULL') . "\n";
    echo "- Room: " . ($first->section?->room?->room_name ?? 'NULL') . "\n";
    echo "- Term: " . ($first->section?->term?->term_name ?? 'NULL') . "\n";
}

echo "\n=== DONE ===\n";