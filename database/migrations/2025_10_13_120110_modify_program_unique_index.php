<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyProgramUniqueIndex extends Migration
{
    public function up()
    {
        $dbName = DB::getDatabaseName();
        $idxs = DB::select('SELECT INDEX_NAME FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?', [$dbName, 'tblprogram']);
        $existing = array_map(function ($r) { return $r->INDEX_NAME; }, $idxs);
        $toDrop = ['tblprogram_program_code_unique', 'program_code'];
        foreach ($toDrop as $name) {
            if (in_array($name, $existing)) {
                try { DB::statement("ALTER TABLE `tblprogram` DROP INDEX `{$name}`"); } catch (\Exception $e) {}
            }
        }
        if (!in_array('tblprogram_program_code_is_deleted_unique', $existing)) {
            Schema::table('tblprogram', function (Blueprint $table) {
                $table->unique(['program_code', 'is_deleted'], 'tblprogram_program_code_is_deleted_unique');
            });
        }
    }

    public function down()
    {
        try { Schema::table('tblprogram', function (Blueprint $table) { $table->dropUnique('tblprogram_program_code_is_deleted_unique'); }); } catch (\Exception $e) {}
        try { Schema::table('tblprogram', function (Blueprint $table) { $table->unique('program_code', 'tblprogram_program_code_unique'); }); } catch (\Exception $e) {}
    }
}
