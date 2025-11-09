<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\SoftDeleteFlag;

class Enrollment extends Model
{
    use HasFactory;
    use SoftDeleteFlag;

    protected $table = 'tblenrollment';
    protected $primaryKey = 'enrollment_id';
    public $timestamps = false;

    protected $fillable = [
        'student_id',
        'section_id',
        'course_id',
        'date_enrolled',
        'status',
        'letter_grade',
        'is_deleted'
    ];

    protected $casts = [
        'date_enrolled' => 'datetime',
    ];

    public function student()
    { 
        return $this->belongsTo(Student::class,'student_id','student_id'); 
    }

    public function section()
    { 
        return $this->belongsTo(Section::class,'section_id','section_id'); 
    }

    public function course()
    { 
        return $this->belongsTo(Course::class,'course_id','course_id'); 
    }

    public static function getAvailableStatuses()
    {
        return ['enrolled', 'dropped', 'completed', 'irregular'];
    }
}
