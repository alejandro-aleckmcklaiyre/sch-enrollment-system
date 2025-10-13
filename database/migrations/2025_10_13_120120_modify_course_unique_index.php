<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyCourseUniqueIndex extends Migration
{
    public function up()
    {
        $dbName = DB::getDatabaseName();
        $idxs = DB::select('SELECT INDEX_NAME FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?', [$dbName, 'tblcourse']);
        $existing = array_map(function ($r) { return $r->INDEX_NAME; }, $idxs);
        $toDrop = ['tblcourse_course_code_unique', 'course_code'];
        foreach ($toDrop as $name) {
            if (in_array($name, $existing)) {
                try { DB::statement("ALTER TABLE `tblcourse` DROP INDEX `{$name}`"); } catch (\Exception $e) {}
            }
        }
        if (!in_array('tblcourse_course_code_is_deleted_unique', $existing)) {
            Schema::table('tblcourse', function (Blueprint $table) {
                $table->unique(['course_code', 'is_deleted'], 'tblcourse_course_code_is_deleted_unique');
            });
        }
    }

    public function down()
    {
        try { Schema::table('tblcourse', function (Blueprint $table) { $table->dropUnique('tblcourse_course_code_is_deleted_unique'); }); } catch (\Exception $e) {}
        try { Schema::table('tblcourse', function (Blueprint $table) { $table->unique('course_code', 'tblcourse_course_code_unique'); }); } catch (\Exception $e) {}
    }
}
