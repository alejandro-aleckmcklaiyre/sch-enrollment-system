<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $table = 'tblsection';
    protected $primaryKey = 'section_id';
    public $timestamps = false;

    protected $fillable = [
        'section_code','course_id','term_id','instructor_id','day_pattern','start_time','end_time','room_id','max_capacity'
    ];

    public function course(){ return $this->belongsTo(Course::class,'course_id','course_id'); }
    public function instructor(){ return $this->belongsTo(Instructor::class,'instructor_id','instructor_id'); }
    public function room(){ return $this->belongsTo(Room::class,'room_id','room_id'); }
    public function term(){ return $this->belongsTo(Term::class,'term_id','term_id'); }
}
