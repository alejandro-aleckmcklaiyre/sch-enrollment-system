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
    public static function getSearchableColumns(): array
    {
        return ['student_no', 'last_name', 'first_name', 'email'];
    }
    public static function getAllowedSorts(): array
    {
        return ['student_id', 'student_no', 'last_name', 'first_name', 'email', 'year_level', 'birthdate'];
    }

    protected $fillable = [
        'student_id',
        'student_no',
        'last_name',
        'first_name',
        'middle_name',
        'email',
        'gender',
        'birthdate',
        'year_level',
        'program_id',
        'user_id',
        'is_deleted',
    ];

    public function program()
    {
        return $this->belongsTo(\App\Models\Program::class, 'program_id', 'program_id');
    }

    public function enrollments()
    {
        return $this->hasMany(\App\Models\Enrollment::class, 'student_id', 'student_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }
}
