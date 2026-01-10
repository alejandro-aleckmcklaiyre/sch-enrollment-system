<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\SoftDeleteFlag;

class Term extends Model
{
    use HasFactory;
    use SoftDeleteFlag;

    protected $table = 'tblterm';
    protected $primaryKey = 'term_id';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = ['term_id','term_code','start_date','end_date','is_deleted'];
    
    protected $casts = [
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d'
    ];

    protected $dates = ['start_date', 'end_date'];

    // Scope to get the current active term based on dates
    public function scopeCurrent($query)
    {
        return $query->where('is_deleted', 0)
            ->where('start_date', '<=', now()->toDateString())
            ->where('end_date', '>=', now()->toDateString())
            ->first();
    }

    // Static method to get current term
    public static function getCurrentTerm()
    {
        return self::where('is_deleted', 0)
            ->where('start_date', '<=', now()->toDateString())
            ->where('end_date', '>=', now()->toDateString())
            ->first();
    }

    public function sections()
    {
        return $this->hasMany(Section::class, 'term_id', 'term_id');
    }

    // Accessor to get term name based on code
    public function getTermNameAttribute()
    {
        return $this->term_code ?? 'Unknown Term';
    }
}
