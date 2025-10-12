<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    use HasFactory;

    protected $table = 'tblinstructor';
    protected $primaryKey = 'instructor_id';
    public $timestamps = false;

    protected $fillable = [
        'last_name','first_name','email','dept_id'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_id', 'dept_id');
    }
}
