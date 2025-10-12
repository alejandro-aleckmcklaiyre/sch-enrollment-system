<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoursePrerequisite extends Model
{
    protected $table = 'tblcourse_prerequisite';
    public $timestamps = false;
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = ['course_id','prereq_course_id'];

    public function course(){ return $this->belongsTo(Course::class,'course_id','course_id'); }
    public function prereq(){ return $this->belongsTo(Course::class,'prereq_course_id','course_id'); }
}
