<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\SoftDeleteFlag;

class Department extends Model
{
    use HasFactory;
    use SoftDeleteFlag;

    protected $table = 'tbldepartment';
    protected $primaryKey = 'dept_id';
    public $timestamps = false;

    protected $fillable = [
        'dept_code',
        'dept_name',
        'is_deleted',
    ];
}
