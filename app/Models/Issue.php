<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;

class Issue extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at'];
    protected $casts = [
        'date' => 'date:d/m/Y', // O 'datetime' si tiene hora
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments')
            ->onlyKeepLatest(3)
            ->useDisk('public');
    }

    public function plan_category(){
        return $this->belongsTo(PlanCategory::class);
    }

    public function candidate(){
        return $this->belongsTo(Candidate::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function scopeFilterByDate($query, $date){
        if( !$date ){ return $query; }
        return $query->where('date', $date);
    }

    public function scopeFilterByCandidate($query, $candidateId)
    {
        if (!$candidateId) {
            return $query;
        }
        return $query->where('candidate_id', $candidateId);
    }

    public function scopeFilterByDates($query, $start, $end)
    {
        if (!$start || !$end) {
            return $query;
        }
        return $query->whereBetween('date', [$start, $end]);
    }
}
