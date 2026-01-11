<?php
require 'vendor/autoload.php';
require 'bootstrap/app.php';

use App\Models\User;
use App\Models\Student;

// Get Aleck Alejandro user
$aleckUser = User::where('name', 'Aleck Alejandro')->first();
echo "Aleck User ID: " . ($aleckUser ? $aleckUser->id : 'Not found') . "\n";
echo "Aleck User Email: " . ($aleckUser ? $aleckUser->email : 'Not found') . "\n";

// Get Kevin Barcelos user
$kevinUser = User::where('name', 'Kevin Joseph Barcelos')->orWhere('name', 'Kevin Barcelos')->first();
echo "\nKevin User ID: " . ($kevinUser ? $kevinUser->id : 'Not found') . "\n";
echo "Kevin User Email: " . ($kevinUser ? $kevinUser->email : 'Not found') . "\n";

// Get student for Aleck
if ($aleckUser) {
    $aleckStudent = $aleckUser->student;
    echo "\nAleck Student: " . ($aleckStudent ? $aleckStudent->first_name . ' ' . $aleckStudent->last_name : 'None') . "\n";
    echo "Aleck Student user_id: " . ($aleckStudent ? $aleckStudent->user_id : 'N/A') . "\n";
}

// Get student for Kevin
if ($kevinUser) {
    $kevinStudent = $kevinUser->student;
    echo "\nKevin Student: " . ($kevinStudent ? $kevinStudent->first_name . ' ' . $kevinStudent->last_name : 'None') . "\n";
    echo "Kevin Student user_id: " . ($kevinStudent ? $kevinStudent->user_id : 'N/A') . "\n";
}

// Show all students and their user_ids
echo "\n\nAll students with user_ids:\n";
$allStudents = Student::select('student_id', 'first_name', 'last_name', 'user_id')->get();
foreach ($allStudents as $s) {
    echo "Student: " . $s->first_name . " " . $s->last_name . " (ID: " . $s->student_id . ") - user_id: " . $s->user_id . "\n";
}

echo "\n\nAll users:\n";
$allUsers = User::where('role', 'student')->select('id', 'name', 'email', 'role')->get();
foreach ($allUsers as $u) {
    echo "User: " . $u->name . " (ID: " . $u->id . ") - Email: " . $u->email . "\n";
}
