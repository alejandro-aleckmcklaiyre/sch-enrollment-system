<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the composite unique indexes that prevent soft delete
        $indexes = [
            'tblroom' => 'tblroom_room_code_is_deleted_unique',
            'tblprogram' => 'tblprogram_program_code_is_deleted_unique',
            'tblstudent' => 'tblstudent_student_no_is_deleted_unique',
            'tblinstructor' => 'tblinstructor_email_is_deleted_unique',
            'tbldepartment' => 'tbldepartment_dept_code_is_deleted_unique',
        ];

        foreach ($indexes as $table => $index) {
            try {
                DB::statement("ALTER TABLE `{$table}` DROP INDEX `{$index}`");
            } catch (\Exception $e) {
                // Index doesn't exist, continue
            }
        }

        // Note: Uniqueness is enforced in application code via controllers
    }

    public function down(): void
    {
        // Recreate the composite unique indexes
        Schema::table('tblroom', function (Blueprint $table) {
            $table->unique(['room_code', 'is_deleted'], 'tblroom_room_code_is_deleted_unique');
        });
        Schema::table('tblprogram', function (Blueprint $table) {
            $table->unique(['program_code', 'is_deleted'], 'tblprogram_program_code_is_deleted_unique');
        });
        Schema::table('tblstudent', function (Blueprint $table) {
            $table->unique(['student_no', 'is_deleted'], 'tblstudent_student_no_is_deleted_unique');
        });
        Schema::table('tblinstructor', function (Blueprint $table) {
            $table->unique(['email', 'is_deleted'], 'tblinstructor_email_is_deleted_unique');
        });
        Schema::table('tbldepartment', function (Blueprint $table) {
            $table->unique(['dept_code', 'is_deleted'], 'tbldepartment_dept_code_is_deleted_unique');
        });
    }
};
