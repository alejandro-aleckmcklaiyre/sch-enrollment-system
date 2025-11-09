<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

try {
    if (!Schema::hasColumn('tblenrollment', 'course_id')) {
        DB::statement('ALTER TABLE tblenrollment ADD COLUMN course_id INT NULL AFTER section_id');
        echo "Added course_id column to tblenrollment\n";
    } else {
        echo "course_id already exists\n";
    }
    // Try to add foreign key if tblcourse exists and no FK yet
    $hasCourseTable = Schema::hasTable('tblcourse');
    if ($hasCourseTable) {
        try {
            DB::statement('ALTER TABLE tblenrollment ADD CONSTRAINT tblenrollment_course_fk FOREIGN KEY (course_id) REFERENCES tblcourse(course_id) ON DELETE SET NULL');
            echo "Added foreign key tblenrollment_course_fk\n";
        } catch (\Exception $e) {
            echo "Could not add foreign key: " . $e->getMessage() . "\n";
        }
    } else {
        echo "tblcourse does not exist; skipped FK creation\n";
    }
} catch (\Exception $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}
