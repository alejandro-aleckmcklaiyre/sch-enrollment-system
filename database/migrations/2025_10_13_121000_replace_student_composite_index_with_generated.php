<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ReplaceStudentCompositeIndexWithGenerated extends Migration
{
    public function up()
    {
        $table = 'tblstudent';
        $dbName = DB::getDatabaseName();

        // collect existing indexes
        $idxs = DB::select('SELECT INDEX_NAME FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?', [$dbName, $table]);
        $existing = array_map(function ($r) { return $r->INDEX_NAME; }, $idxs);

        // drop composite index if present
        if (in_array('tblstudent_student_no_is_deleted_unique', $existing)) {
            try { DB::statement("ALTER TABLE `{$table}` DROP INDEX `tblstudent_student_no_is_deleted_unique`"); } catch (\Exception $e) {}
        }

        // add generated column active_student_no if missing
        $cols = DB::select('SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?', [$dbName, $table]);
        $colNames = array_map(function($r){ return $r->COLUMN_NAME; }, $cols);
        if (!in_array('active_student_no', $colNames)) {
            // virtual generated column: student_no when is_deleted=0, else NULL
            DB::statement("ALTER TABLE `{$table}` ADD COLUMN `active_student_no` VARCHAR(255) GENERATED ALWAYS AS (CASE WHEN (COALESCE(`is_deleted`,0) = 0) THEN `student_no` ELSE NULL END) VIRTUAL");
        }

        // add unique index on generated column
        $idxs = DB::select('SELECT INDEX_NAME FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?', [$dbName, $table]);
        $existing = array_map(function ($r) { return $r->INDEX_NAME; }, $idxs);
        if (!in_array('tblstudent_active_student_no_unique', $existing)) {
            try { DB::statement("ALTER TABLE `{$table}` ADD UNIQUE INDEX `tblstudent_active_student_no_unique` (`active_student_no`)"); } catch (\Exception $e) {}
        }
    }

    public function down()
    {
        $table = 'tblstudent';
        try { DB::statement("ALTER TABLE `{$table}` DROP INDEX `tblstudent_active_student_no_unique`"); } catch (\Exception $e) {}
        try { DB::statement("ALTER TABLE `{$table}` DROP COLUMN `active_student_no`"); } catch (\Exception $e) {}
        try { DB::statement("ALTER TABLE `{$table}` ADD UNIQUE INDEX `tblstudent_student_no_is_deleted_unique` (`student_no`,`is_deleted`)"); } catch (\Exception $e) {}
    }
}
