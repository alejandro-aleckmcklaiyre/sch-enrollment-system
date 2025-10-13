<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\SoftDeleteFlag;

class Program extends Model
{
    protected $table = 'tblprogram';
    protected $primaryKey = 'program_id';
    public $timestamps = false;
    use SoftDeleteFlag;

    protected $fillable = [
        'program_code',
        'program_name',
        'is_deleted',
        'dept_id',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_id', 'dept_id');
    }
}
