<?php
// Usage: php scripts/create_test_student_enrollment.php
require_once __DIR__ . '/../vendor/autoload.php';
use Illuminate\Database\Capsule\Manager as DB;
use App\Models\User;
use App\Models\Student;
use App\Models\Program;
use App\Models\Course;
use App\Models\Section;
use App\Models\Enrollment;

// Find the user by email
$user = User::where('email', 'student@test.com')->first();
if (!$user) {
    exit("User student@test.com not found.\n");
}

// Find or create a program
$program = Program::first();
if (!$program) {
    exit("No program found. Please create a program first.\n");
}

// Find or create a student profile
$student = Student::where('user_id', $user->id)->first();
if (!$student) {
    $student = Student::create([
        'user_id' => $user->id,
        'student_no' => '20260001',
        'first_name' => $user->name ?? 'Test',
        'last_name' => 'Student',
        'email' => $user->email,
        'year_level' => 1,
        'program_id' => $program->program_id,
        'is_deleted' => 0,
    ]);
    echo "Created student profile.\n";
} else {
    // Update program if missing
    if (!$student->program_id) {
        $student->program_id = $program->program_id;
        $student->save();
        echo "Updated student program.\n";
    }
}

// Find a course and section
$course = Course::first();
if (!$course) {
    exit("No course found. Please create a course first.\n");
}
$section = Section::where('course_id', $course->course_id)->first();
if (!$section) {
    exit("No section found for course. Please create a section first.\n");
}

// Create a test enrollment if none exists
$enrollment = Enrollment::where('student_id', $student->student_id)
    ->where('section_id', $section->section_id)
    ->first();
if (!$enrollment) {
    Enrollment::create([
        'student_id' => $student->student_id,
        'section_id' => $section->section_id,
        'status' => 'ENROLLED',
        'is_deleted' => 0,
    ]);
    echo "Created test enrollment.\n";
} else {
    echo "Test enrollment already exists.\n";
}

echo "Done.\n";
