<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';

use App\Models\Enrollment;

$enrollments = Enrollment::with(['section.course', 'section.term'])->where('student_id', 1)->where('is_deleted', 0)->get();

echo "Total enrollments: " . $enrollments->count() . PHP_EOL;

$validCount = 0;
$invalidCount = 0;

foreach ($enrollments as $enrollment) {
    $hasSection = $enrollment->section !== null;
    $hasCourse = $hasSection && $enrollment->section->course !== null;
    $hasTerm = $hasSection && $enrollment->section->term !== null;

    if ($hasSection && $hasCourse && $hasTerm) {
        $validCount++;
    } else {
        $invalidCount++;
        echo "Invalid enrollment ID {$enrollment->enrollment_id}: Section=" . ($hasSection ? $enrollment->section->section_id : 'NULL') .
             ", Course=" . ($hasCourse ? $enrollment->section->course->course_id : 'NULL') .
             ", Term=" . ($hasTerm ? $enrollment->section->term->term_id : 'NULL') . PHP_EOL;
    }
}

echo "Valid enrollments: $validCount" . PHP_EOL;
echo "Invalid enrollments: $invalidCount" . PHP_EOL;
