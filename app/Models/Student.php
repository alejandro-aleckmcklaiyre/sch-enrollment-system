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
