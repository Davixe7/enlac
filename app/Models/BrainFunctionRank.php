<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrainFunctionRank extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = [
        'caracteristic' => 'string', // Para manejar el enum como string
        'laterality_impact' => 'string', // Para manejar el enum como string
    ];

    public function brainLevel()
    {
        return $this->belongsTo(BrainLevel::class);
    }

    public function brainFunction()
    {
        return $this->belongsTo(BrainFunction::class);
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
