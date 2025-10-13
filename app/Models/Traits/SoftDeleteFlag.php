<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait SoftDeleteFlag
{
    public static function bootSoftDeleteFlag()
    {
        static::addGlobalScope('is_deleted', function (Builder $builder) {
            $builder->where(function($q){ $q->where('is_deleted', 0)->orWhereNull('is_deleted'); });
        });
    }

    public function delete()
    {
        // soft flag
        $this->is_deleted = 1;
        return $this->save();
    }

    public function restore()
    {
        $this->is_deleted = 0;
        return $this->save();
    }

    // allow querying including deleted
    public static function withTrashed()
    {
        return (new static)->newQueryWithoutScope('is_deleted');
    }
}
