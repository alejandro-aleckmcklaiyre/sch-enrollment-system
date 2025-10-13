<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyStudentUniqueIndex extends Migration
{
    public function up()
    {
        $dbName = DB::getDatabaseName();
        $idxs = DB::select('SELECT INDEX_NAME FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?', [$dbName, 'tblstudent']);
        $existing = array_map(function ($r) { return $r->INDEX_NAME; }, $idxs);
        $toDrop = ['tblstudent_student_no_unique', 'student_no'];
        foreach ($toDrop as $name) {
            if (in_array($name, $existing)) {
                try { DB::statement("ALTER TABLE `tblstudent` DROP INDEX `{$name}`"); } catch (\Exception $e) {}
            }
        }
        if (!in_array('tblstudent_student_no_is_deleted_unique', $existing)) {
            Schema::table('tblstudent', function (Blueprint $table) {
                $table->unique(['student_no', 'is_deleted'], 'tblstudent_student_no_is_deleted_unique');
            });
        }
    }

    public function down()
    {
        try { Schema::table('tblstudent', function (Blueprint $table) { $table->dropUnique('tblstudent_student_no_is_deleted_unique'); }); } catch (\Exception $e) {}
        try { Schema::table('tblstudent', function (Blueprint $table) { $table->unique('student_no', 'tblstudent_student_no_unique'); }); } catch (\Exception $e) {}
    }
}
