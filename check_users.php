<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking authentication and user data...\n";

// Check if there are any users
$users = DB::table('users')->get();
echo "Total users: " . $users->count() . "\n";

foreach($users as $user) {
    echo "User ID: {$user->id}, Email: {$user->email}, Role: " . ($user->role ?? 'NULL') . "\n";

    // Check if user has student record
    $student = DB::table('tblstudent')->where('user_id', $user->id)->first();
    if ($student) {
        echo "  -> Has student record: {$student->first_name} {$student->last_name} (ID: {$student->student_id})\n";

        // Check enrollments
        $enrollments = DB::table('tblenrollment')->where('student_id', $student->student_id)->where('is_deleted', 0)->count();
        echo "  -> Enrollments: $enrollments\n";
    } else {
        echo "  -> No student record found\n";
    }
}