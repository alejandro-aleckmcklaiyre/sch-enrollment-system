<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

$app = require_once 'bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

try {
    $result = DB::select('SELECT 1 as test');
    echo "Database connected successfully\n";
    echo "Test query result: " . $result[0]->test . "\n";
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
}