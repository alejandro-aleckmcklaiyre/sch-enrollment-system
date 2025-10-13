<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyRoomUniqueIndex extends Migration
{
    public function up()
    {
        $dbName = DB::getDatabaseName();
        $idxs = DB::select('SELECT INDEX_NAME FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?', [$dbName, 'tblroom']);
        $existing = array_map(function ($r) { return $r->INDEX_NAME; }, $idxs);
        $toDrop = ['tblroom_room_code_unique', 'room_code'];
        foreach ($toDrop as $name) {
            if (in_array($name, $existing)) {
                try { DB::statement("ALTER TABLE `tblroom` DROP INDEX `{$name}`"); } catch (\Exception $e) {}
            }
        }
        if (!in_array('tblroom_room_code_is_deleted_unique', $existing)) {
            Schema::table('tblroom', function (Blueprint $table) {
                $table->unique(['room_code', 'is_deleted'], 'tblroom_room_code_is_deleted_unique');
            });
        }
    }

    public function down()
    {
        try { Schema::table('tblroom', function (Blueprint $table) { $table->dropUnique('tblroom_room_code_is_deleted_unique'); }); } catch (\Exception $e) {}
        try { Schema::table('tblroom', function (Blueprint $table) { $table->unique('room_code', 'tblroom_room_code_unique'); }); } catch (\Exception $e) {}
    }
}
