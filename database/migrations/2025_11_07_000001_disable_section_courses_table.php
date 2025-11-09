<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DisableSectionCoursesTable extends Migration
{
    public function up()
    {
        // Ensure that the accidental pivot table is removed if it exists.
        // This migration is intentionally safe and reversible by keeping down() a no-op.
        Schema::dropIfExists('tblsection_courses');
    }

    public function down()
    {
        // no-op: original pivot migration is disabled in repo history if needed to be restored
    }
}
