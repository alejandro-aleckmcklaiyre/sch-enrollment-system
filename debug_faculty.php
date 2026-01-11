<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::find(7);
echo "=== USER ===\n";
echo json_encode($user, JSON_PRETTY_PRINT) . "\n\n";

$instructor = \App\Models\Instructor::find(99);
echo "=== INSTRUCTOR ===\n";
echo json_encode($instructor, JSON_PRETTY_PRINT) . "\n\n";

echo "=== USER->INSTRUCTOR RELATIONSHIP ===\n";
echo json_encode($user->instructor(), JSON_PRETTY_PRINT) . "\n\n";

$userInstructor = $user->instructor;
echo "=== USER->INSTRUCTOR (loaded) ===\n";
echo json_encode($userInstructor, JSON_PRETTY_PRINT) . "\n\n";

$sections = \App\Models\Section::where('instructor_id', 99)->get();
echo "=== SECTIONS FOR INSTRUCTOR 99 ===\n";
echo "Count: " . $sections->count() . "\n";
echo json_encode($sections, JSON_PRETTY_PRINT) . "\n";
