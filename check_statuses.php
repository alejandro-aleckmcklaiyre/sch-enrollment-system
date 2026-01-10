<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use App\Models\Enrollment;

$app = require_once 'bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$statuses = Enrollment::where('student_id', 1)->pluck('status')->unique();
echo "Unique status values for student 1: " . $statuses->implode(', ') . "\n";

$allStatuses = Enrollment::pluck('status')->unique();
echo "All unique status values in database: " . $allStatuses->implode(', ') . "\n";