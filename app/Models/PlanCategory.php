<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanCategory extends Model
{
    protected $guarded = [];

    public function parent(){
        return $this->belongsTo(PlanCategory::class, 'parent_id');
    }

    public function children(){
        return $this->hasMany(PlanCategory::class, 'parent_id');
    }

    public function scopeByParent($query, $parent_id){
        return $query->whereParentId($parent_id);
    }

    public function scopeBaseOnly($query, $filter){
        if(!$filter){ return $query; }
        return $query->where('parent_id', null);
    }
}
