<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $table = 'tblprogram';
    protected $primaryKey = 'program_id';
    public $timestamps = false;

    protected $fillable = [
        'program_code',
        'program_name',
        'dept_id',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_id', 'dept_id');
    }
}
