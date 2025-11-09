<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyTermCodeUniqueIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE tblterm DROP INDEX term_code');
        DB::statement('ALTER TABLE tblterm ADD UNIQUE term_code_unique(term_code, is_deleted)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE tblterm DROP INDEX term_code_unique');
        DB::statement('ALTER TABLE tblterm ADD UNIQUE term_code(term_code)');
    }
}