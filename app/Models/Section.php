<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\SoftDeleteFlag;

class Section extends Model
{
    use HasFactory;
    use SoftDeleteFlag;

    protected $table = 'tblsection';
    protected $primaryKey = 'section_id';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'section_id',
        'section_code','course_id','term_id','instructor_id','day_pattern','start_time','end_time','room_id','max_capacity','is_deleted'
    ];

    public function course(){ return $this->belongsTo(Course::class,'course_id','course_id'); }
    public function instructor(){ return $this->belongsTo(Instructor::class,'instructor_id','instructor_id'); }
    public function room(){ return $this->belongsTo(Room::class,'room_id','room_id'); }
    public function term(){ return $this->belongsTo(Term::class,'term_id','term_id'); }
    public function enrollments(){ return $this->hasMany(Enrollment::class,'section_id','section_id'); }
}
