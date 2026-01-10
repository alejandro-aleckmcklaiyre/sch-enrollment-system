<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use App\Models\User;
use App\Models\Enrollment;

$app = require_once 'bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

echo "=== CONTROLLER SIMULATION ===\n\n";

// Get the student user
$user = User::where('email', 'student@test.com')->first();
$student = $user->student;

echo "Student: {$student->first_name} {$student->last_name} (ID: {$student->student_id})\n\n";

$enrollments = Enrollment::where('student_id', $student->student_id)
    ->where('is_deleted', 0)
    ->with(['section.course', 'section.instructor', 'section.term', 'section.room'])
    ->orderBy('date_enrolled', 'desc')
    ->get();

echo "Enrollments count: {$enrollments->count()}\n\n";

if ($enrollments->count() > 0) {
    echo "First enrollment details:\n";
    $first = $enrollments->first();
    echo "- Enrollment ID: {$first->enrollment_id}\n";
    echo "- Section ID: {$first->section_id}\n";
    echo "- Section loaded: " . (!is_null($first->section) ? 'YES' : 'NO') . "\n";

    if ($first->section) {
        echo "- Section code: " . ($first->section->section_code ?? 'NULL') . "\n";
        echo "- Course loaded: " . (!is_null($first->section->course) ? 'YES' : 'NO') . "\n";
        echo "- Course code: " . ($first->section->course?->course_code ?? 'NULL') . "\n";
        echo "- Course title: " . ($first->section->course?->course_title ?? 'NULL') . "\n";
        echo "- Instructor loaded: " . (!is_null($first->section->instructor) ? 'YES' : 'NO') . "\n";
        echo "- Instructor name: " . ($first->section->instructor?->first_name ?? 'NULL') . " " . ($first->section->instructor?->last_name ?? '') . "\n";
        echo "- Term loaded: " . (!is_null($first->section->term) ? 'YES' : 'NO') . "\n";
        echo "- Term name: " . ($first->section->term?->term_name ?? 'NULL') . "\n";
        echo "- Room loaded: " . (!is_null($first->section->room) ? 'YES' : 'NO') . "\n";
        echo "- Room number: " . ($first->section->room?->room_number ?? 'NULL') . "\n";
    }
}

echo "\n=== CHECKING RELATIONSHIP LOADING ===\n";

// Check if relationships are loaded
$first = $enrollments->first();
if ($first) {
    echo "Section relationship loaded: " . ($first->relationLoaded('section') ? 'YES' : 'NO') . "\n";
    if ($first->section) {
        echo "Course relationship loaded on section: " . ($first->section->relationLoaded('course') ? 'YES' : 'NO') . "\n";
        echo "Instructor relationship loaded on section: " . ($first->section->relationLoaded('instructor') ? 'YES' : 'NO') . "\n";
        echo "Term relationship loaded on section: " . ($first->section->relationLoaded('term') ? 'YES' : 'NO') . "\n";
        echo "Room relationship loaded on section: " . ($first->section->relationLoaded('room') ? 'YES' : 'NO') . "\n";
    }
}

echo "\n=== DONE ===\n";