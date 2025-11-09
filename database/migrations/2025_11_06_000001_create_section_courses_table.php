<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSectionCoursesTable extends Migration
{
    public function up()
    {
        Schema::create('tblsection_courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('section_id');
            $table->unsignedBigInteger('course_id');
            $table->tinyInteger('is_deleted')->default(0);
            $table->timestamps();

            $table->foreign('section_id')->references('section_id')->on('tblsection');
            $table->foreign('course_id')->references('course_id')->on('tblcourse');
            
            // Prevent duplicate section-course combinations
            $table->unique(['section_id', 'course_id', 'is_deleted']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tblsection_courses');
    }
}