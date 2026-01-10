<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

$app = require_once 'bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$user = \App\Models\User::where('email', 'student@test.com')->first();
if (!$user) {
    echo "User not found\n";
    exit;
}

$student = $user->student;
if (!$student) {
    echo "Student not found\n";
    exit;
}

$enrollments = \App\Models\Enrollment::where('student_id', $student->student_id)
    ->where('is_deleted', 0)
    ->with(['section.course', 'section.instructor', 'section.term'])
    ->get();

echo "Found {$enrollments->count()} enrollments\n";

foreach ($enrollments as $enrollment) {
    echo "Enrollment {$enrollment->enrollment_id}: section_id={$enrollment->section_id}\n";
    
    if ($enrollment->section) {
        echo "  Section loaded: {$enrollment->section->section_code}\n";
        echo "  Section course_id: {$enrollment->section->course_id}\n";
        
        if ($enrollment->section->course) {
            echo "  Course loaded: {$enrollment->section->course->course_title}\n";
        } else {
            echo "  Course NOT loaded\n";
            
            // Try manual lookup
            $course = \App\Models\Course::find($enrollment->section->course_id);
            echo "  Manual course lookup: " . ($course ? $course->course_title : "NOT FOUND") . "\n";
        }
    } else {
        echo "  Section NOT loaded\n";
    }
    echo "---\n";
}