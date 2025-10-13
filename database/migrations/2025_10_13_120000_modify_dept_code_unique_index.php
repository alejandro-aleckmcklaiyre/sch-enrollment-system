<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyDeptCodeUniqueIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Use raw checks against INFORMATION_SCHEMA to determine existing index names and drop
        // only if present. This avoids SQL errors when the expected index name differs.
        $dbName = DB::getDatabaseName();

        $idxs = DB::select(
            'SELECT INDEX_NAME FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?',
            [$dbName, 'tbldepartment']
        );

        $existing = array_map(function ($r) { return $r->INDEX_NAME; }, $idxs);

        // Common generated index names to attempt to drop if present
        $toDrop = [
            'tbldepartment_dept_code_unique', // default Laravel name
            'dept_code', // sometimes index may be named after the column
        ];

        foreach ($toDrop as $name) {
            if (in_array($name, $existing)) {
                try {
                    DB::statement("ALTER TABLE `tbldepartment` DROP INDEX `{$name}`");
                } catch (\Exception $e) {
                    // ignore any error dropping the index
                }
            }
        }

        // Finally create composite Unique index if not already present
        if (!in_array('tbldepartment_dept_code_is_deleted_unique', $existing)) {
            Schema::table('tbldepartment', function (Blueprint $table) {
                $table->unique(['dept_code', 'is_deleted'], 'tbldepartment_dept_code_is_deleted_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbldepartment', function (Blueprint $table) {
            try {
                $table->dropUnique('tbldepartment_dept_code_is_deleted_unique');
            } catch (\Exception $e) {
                // ignore
            }

            // restore single-column unique on dept_code (use explicit name)
            $table->unique('dept_code', 'tbldepartment_dept_code_unique');
        });
    }
}
