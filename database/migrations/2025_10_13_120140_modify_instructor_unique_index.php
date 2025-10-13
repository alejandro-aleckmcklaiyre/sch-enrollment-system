<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyInstructorUniqueIndex extends Migration
{
    public function up()
    {
        $dbName = DB::getDatabaseName();
        $idxs = DB::select('SELECT INDEX_NAME FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?', [$dbName, 'tblinstructor']);
        $existing = array_map(function ($r) { return $r->INDEX_NAME; }, $idxs);
        $toDrop = ['tblinstructor_email_unique', 'email'];
        foreach ($toDrop as $name) {
            if (in_array($name, $existing)) {
                try { DB::statement("ALTER TABLE `tblinstructor` DROP INDEX `{$name}`"); } catch (\Exception $e) {}
            }
        }
        if (!in_array('tblinstructor_email_is_deleted_unique', $existing)) {
            Schema::table('tblinstructor', function (Blueprint $table) {
                $table->unique(['email', 'is_deleted'], 'tblinstructor_email_is_deleted_unique');
            });
        }
    }

    public function down()
    {
        try { Schema::table('tblinstructor', function (Blueprint $table) { $table->dropUnique('tblinstructor_email_is_deleted_unique'); }); } catch (\Exception $e) {}
        try { Schema::table('tblinstructor', function (Blueprint $table) { $table->unique('email', 'tblinstructor_email_unique'); }); } catch (\Exception $e) {}
    }
}
