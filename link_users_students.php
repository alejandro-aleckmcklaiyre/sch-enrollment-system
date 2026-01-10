<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Linking users to student records...\n";

// Get users without student records
$usersWithoutStudents = DB::table('users')
    ->where('role', 'student')
    ->whereNotExists(function($query) {
        $query->select(DB::raw(1))
              ->from('tblstudent')
              ->whereRaw('tblstudent.user_id = users.id');
    })
    ->get();

// Get students without user_ids
$studentsWithoutUsers = DB::table('tblstudent')
    ->where('is_deleted', 0)
    ->where(function($query) {
        $query->whereNull('user_id')
              ->orWhere('user_id', '');
    })
    ->orderBy('student_id')
    ->get();

echo "Users without students: " . $usersWithoutStudents->count() . "\n";
echo "Students without users: " . $studentsWithoutUsers->count() . "\n";

if ($usersWithoutStudents->count() > 0 && $studentsWithoutUsers->count() > 0) {
    $studentsArray = $studentsWithoutUsers->toArray();

    foreach($usersWithoutStudents as $index => $user) {
        if (isset($studentsArray[$index])) {
            $student = $studentsArray[$index];

            // Update the student record to link to this user
            DB::table('tblstudent')
                ->where('student_id', $student->student_id)
                ->update(['user_id' => $user->id]);

            echo "Linked User {$user->email} (ID: {$user->id}) to Student {$student->first_name} {$student->last_name} (ID: {$student->student_id})\n";
        }
    }
}

echo "\nFinal check...\n";
$linkedUsers = DB::table('users')
    ->where('role', 'student')
    ->whereExists(function($query) {
        $query->select(DB::raw(1))
              ->from('tblstudent')
              ->whereRaw('tblstudent.user_id = users.id');
    })
    ->count();

echo "Users with student records: $linkedUsers\n";