<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add role column after email if it doesn't exist
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'faculty', 'student'])->default('student')->after('email');
            }
        });

        // Add user_id foreign key to instructors table
        Schema::table('tblinstructor', function (Blueprint $table) {
            if (!Schema::hasColumn('tblinstructor', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->unique();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            }
        });

        // Add user_id foreign key to students table
        Schema::table('tblstudent', function (Blueprint $table) {
            if (!Schema::hasColumn('tblstudent', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->unique();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys first
        Schema::table('tblstudent', function (Blueprint $table) {
            if (Schema::hasColumn('tblstudent', 'user_id')) {
                $table->dropForeignKey('tblstudent_user_id_foreign');
                $table->dropUnique('tblstudent_user_id_unique');
                $table->dropColumn('user_id');
            }
        });

        Schema::table('tblinstructor', function (Blueprint $table) {
            if (Schema::hasColumn('tblinstructor', 'user_id')) {
                $table->dropForeignKey('tblinstructor_user_id_foreign');
                $table->dropUnique('tblinstructor_user_id_unique');
                $table->dropColumn('user_id');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};
