<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::statement('ALTER TABLE tblenrollment MODIFY enrollment_id int(11) NOT NULL AUTO_INCREMENT');
    echo "Set enrollment_id to AUTO_INCREMENT\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
