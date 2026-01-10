<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

// Get test user
$testUser = DB::table('users')->where('email', 'test@example.com')->first();

if (!$testUser) {
    echo "Test user not found\n";
    exit(1);
}

echo "Found test user: ID = {$testUser->id}, Email = {$testUser->email}\n";

// Find a student with enrollments
$studentWithEnrollments = DB::table('tblstudent')
    ->leftJoin('tblenrollment', 'tblstudent.student_id', '=', 'tblenrollment.student_id')
    ->where('tblstudent.is_deleted', 0)
    ->where('tblenrollment.is_deleted', 0)
    ->select('tblstudent.student_id', 'tblstudent.first_name', 'tblstudent.last_name')
    ->distinct()
    ->first();

if (!$studentWithEnrollments) {
    echo "No student with enrollments found\n";
    exit(1);
}

echo "Found student with enrollments: ID = {$studentWithEnrollments->student_id}, Name = {$studentWithEnrollments->first_name} {$studentWithEnrollments->last_name}\n";

// Link the student to the test user
DB::table('tblstudent')
    ->where('student_id', $studentWithEnrollments->student_id)
    ->update(['user_id' => $testUser->id]);

echo "✓ Linked student {$studentWithEnrollments->student_id} to user {$testUser->id}\n";
