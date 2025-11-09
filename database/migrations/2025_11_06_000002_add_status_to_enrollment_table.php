<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToEnrollmentTable extends Migration
{
    public function up()
    {
        Schema::table('tblenrollment', function (Blueprint $table) {
            $table->enum('status', ['enrolled', 'dropped', 'completed', 'irregular'])
                  ->default('enrolled')
                  ->after('section_id');
        });
    }

    public function down()
    {
        Schema::table('tblenrollment', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}