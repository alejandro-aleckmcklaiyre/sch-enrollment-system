<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('email', 'faculty@faculty.com')->first();
$instructor = \App\Models\Instructor::where('email', 'faculty@faculty.com')->first();

if ($user && $instructor) {
    $instructor->update(['user_id' => $user->id]);
    echo "Linked user {$user->id} to instructor {$instructor->instructor_id}\n";
} else {
    echo "User or Instructor not found\n";
    if (!$user) echo "User not found\n";
    if (!$instructor) echo "Instructor not found\n";
}
