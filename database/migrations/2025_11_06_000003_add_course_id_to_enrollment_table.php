<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCourseIdToEnrollmentTable extends Migration
{
    public function up()
    {
        // Safely add a nullable course_id column if it doesn't exist (this matches the imported dump)
        if (!Schema::hasColumn('tblenrollment', 'course_id')) {
            Schema::table('tblenrollment', function (Blueprint $table) {
                $table->integer('course_id')->nullable()->after('section_id');
            });

            // Add foreign key constraint if the referenced table/column exist
            try {
                Schema::table('tblenrollment', function (Blueprint $table) {
                    $table->foreign('course_id', 'tblenrollment_course_fk')
                        ->references('course_id')->on('tblcourse')
                        ->onDelete('set null');
                });
            } catch (\Exception $e) {
                // Ignore if the foreign key cannot be created (e.g., missing tblcourse during import)
                \Log::warning('Could not create foreign key for tblenrollment.course_id: ' . $e->getMessage());
            }
        }
    }

    public function down()
    {
        if (Schema::hasColumn('tblenrollment', 'course_id')) {
            Schema::table('tblenrollment', function (Blueprint $table) {
                // drop foreign key if exists
                try {
                    $table->dropForeign('tblenrollment_course_fk');
                } catch (\Exception $e) {
                    // ignore
                }
                $table->dropColumn('course_id');
            });
        }
    }
}
