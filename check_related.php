<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Courses:\n";
$courses = DB::table('tblcourse')->where('is_deleted', 0)->limit(5)->get();
foreach($courses as $c) {
    echo "ID: {$c->course_id}, Code: {$c->course_code}, Title: {$c->course_title}\n";
}

echo "\nTerms:\n";
$terms = DB::table('tblterm')->where('is_deleted', 0)->get();
foreach($terms as $t) {
    echo "ID: {$t->term_id}, Code: " . ($t->term_code ?? 'NULL') . "\n";
}

echo "\nInstructors:\n";
$instructors = DB::table('tblinstructor')->where('is_deleted', 0)->limit(3)->get();
foreach($instructors as $i) {
    echo "ID: {$i->instructor_id}, Name: {$i->first_name} {$i->last_name}\n";
}

echo "\nRooms:\n";
$rooms = DB::table('tblroom')->where('is_deleted', 0)->limit(3)->get();
foreach($rooms as $r) {
    echo "ID: {$r->room_id}, Number: {$r->room_number}\n";
}