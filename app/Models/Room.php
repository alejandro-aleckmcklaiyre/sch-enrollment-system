<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $table = 'tblroom';
    protected $primaryKey = 'room_id';
    public $timestamps = false;

    protected $fillable = [
        'building','room_code','capacity'
    ];
}
