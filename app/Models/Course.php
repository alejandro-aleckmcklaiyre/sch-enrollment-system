<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\SoftDeleteFlag;

class Course extends Model
{
    protected $table = 'tblcourse';
    protected $primaryKey = 'course_id';
    public $timestamps = false;
    use SoftDeleteFlag;

    protected $fillable = [
        'course_code',
        'course_title',
        'units',
        'lecture_hours',
        'lab_hours',
        'dept_id',
        'is_deleted',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_id', 'dept_id');
    }
}
