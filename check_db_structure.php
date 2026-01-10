<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$app = require_once 'bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

echo "=== DATABASE STRUCTURE CHECK ===\n\n";

echo "Student table columns:\n";
$columns = DB::select('DESCRIBE tblstudent');
foreach($columns as $col) {
    echo "- {$col->Field} ({$col->Type})\n";
}

echo "\nChecking user_id column...\n";
$hasUserId = Schema::hasColumn('tblstudent', 'user_id');
echo "Has user_id: " . ($hasUserId ? 'YES' : 'NO') . "\n";

if($hasUserId) {
    echo "\nSample student user_id values:\n";
    $students = DB::table('tblstudent')->limit(5)->get(['student_id', 'user_id', 'first_name', 'last_name']);
    foreach($students as $s) {
        echo "Student {$s->student_id} ({$s->first_name} {$s->last_name}): user_id = " . ($s->user_id ?? 'NULL') . "\n";
    }

    // Check the specific student from diagnostic
    $student = DB::table('tblstudent')->where('email', 'aleck.alejandro04@gmail.com')->first();
    if($student) {
        echo "\nSpecific student (aleck.alejandro04@gmail.com):\n";
        echo "Student ID: {$student->student_id}, user_id: " . ($student->user_id ?? 'NULL') . "\n";
    }
}

echo "\n=== SECTION CHECK ===\n";
echo "Section table columns:\n";
$sectionColumns = DB::select('DESCRIBE tblsection');
foreach($sectionColumns as $col) {
    echo "- {$col->Field} ({$col->Type})\n";
}

echo "\nSample sections:\n";
$sections = DB::table('tblsection')->limit(3)->get(['section_id', 'section_code', 'course_id', 'instructor_id', 'room_id']);
foreach($sections as $s) {
    echo "Section {$s->section_id} ({$s->section_code}): course_id={$s->course_id}, instructor_id={$s->instructor_id}, room_id={$s->room_id}\n";
}

echo "\n=== DONE ===\n";