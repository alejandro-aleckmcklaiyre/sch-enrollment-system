<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Students without user_id:\n";
$students = DB::table('tblstudent')->where('is_deleted', 0)->whereNull('user_id')->orWhere('user_id', '')->get();
foreach($students as $s) {
    echo "Student ID: {$s->student_id}, Name: {$s->first_name} {$s->last_name}, user_id: " . ($s->user_id ?? 'NULL') . "\n";
}

echo "\nUsers without student records:\n";
$users = DB::table('users')->where('role', 'student')->get();
foreach($users as $u) {
    $student = DB::table('tblstudent')->where('user_id', $u->id)->first();
    if (!$student) {
        echo "User ID: {$u->id}, Email: {$u->email} - NO STUDENT RECORD\n";
    }
}