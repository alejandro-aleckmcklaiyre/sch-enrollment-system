<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

// Get current term
$currentTerm = DB::table('tblterm')
    ->where('is_deleted', 0)
    ->where('start_date', '<=', now()->toDateString())
    ->where('end_date', '>=', now()->toDateString())
    ->first();

if (!$currentTerm) {
    // Set the first term as current by updating dates
    $term = DB::table('tblterm')->where('is_deleted', 0)->first();
    if ($term) {
        DB::table('tblterm')
            ->where('term_id', $term->term_id)
            ->update([
                'start_date' => '2024-01-01',
                'end_date' => '2024-12-31'
            ]);
        $currentTerm = DB::table('tblterm')->where('term_id', $term->term_id)->first();
        echo "Updated term {$term->term_code} dates to current\n";
    }
}

if (!$currentTerm) {
    echo "No current term found\n";
    exit(1);
}

echo "Current term: {$currentTerm->term_code}\n";

// Get some courses
$courses = DB::table('tblcourse')->where('is_deleted', 0)->limit(10)->get();
$instructors = DB::table('tblinstructor')->where('is_deleted', 0)->get();

if ($courses->isEmpty()) {
    echo "No courses found\n";
    exit(1);
}

if ($instructors->isEmpty()) {
    echo "No instructors found\n";
    exit(1);
}

$sectionsCreated = 0;
foreach ($courses as $course) {
    // Check if section already exists for this course and term
    $exists = DB::table('tblsection')
        ->where('course_id', $course->course_id)
        ->where('term_id', $currentTerm->term_id)
        ->where('is_deleted', 0)
        ->exists();

    if (!$exists) {
        $instructor = $instructors->random();
        DB::table('tblsection')->insert([
            'course_id' => $course->course_id,
            'term_id' => $currentTerm->term_id,
            'instructor_id' => $instructor->instructor_id,
            'max_capacity' => 30,
            'is_deleted' => 0
        ]);
        $sectionsCreated++;
        echo "Created section for course {$course->course_code}\n";
    }
}

echo "Created {$sectionsCreated} new sections\n";
