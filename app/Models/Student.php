<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\SoftDeleteFlag;

class Student extends Model
{
    protected $table = 'tblstudent';
    protected $primaryKey = 'student_id';
    public $timestamps = false;

    use SoftDeleteFlag;

    /**
     * Get columns that can be searched
     */
    public static function getSearchableColumns(): array
    {
        return ['student_no', 'last_name', 'first_name', 'email'];
    }

    /**
     * Get allowed sort columns
     */
    public static function getAllowedSorts(): array
    {
        return ['student_id', 'student_no', 'last_name', 'first_name', 'email', 'year_level', 'birthdate'];
    }

    protected $fillable = [
        'student_no',
        'last_name',
        'first_name',
        'middle_name',
        'email',
        'gender',
        'birthdate',
        'year_level',
        'program_id',
        'is_deleted',
    ];

    public function program()
    {
        return $this->belongsTo(\App\Models\Program::class, 'program_id', 'program_id');
    }
}
