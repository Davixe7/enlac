<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanCategory extends Model
{
    protected $guarded = [];

    public function subcategory(){
        return $this->belongsTo(PlanCategory::class);
    }
}
