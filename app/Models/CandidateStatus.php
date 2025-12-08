<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateStatus extends Model
{
    protected $guarded = [];
    protected $hidden  = ['created_at', 'updated_at'];

    public function scopeExcludeByName($query, $names){
        if(!$names){
            return $query;
        }

        $namesArrayToExclude = explode(',', $names);
        return $query->whereNotIn('name', $namesArrayToExclude);
    }
}
