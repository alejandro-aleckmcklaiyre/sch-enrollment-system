<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    use HasFactory;

    protected $table = 'tblterm';
    protected $primaryKey = 'term_id';
    public $timestamps = false;

    protected $fillable = ['term_code','start_date','end_date'];
}
