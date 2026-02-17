<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityDailyScore extends Model
{
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at'];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function scopeFilterByCandidate($query, $candidate_id)
    {
        if (!$candidate_id) {
            return $query;
        }
        return $query->whereCandidateId($candidate_id);
    }

    public function scopeFilterByActivity($query, $activity_id)
    {
        if (!$activity_id) {
            return $query;
        }
        return $query->whereActivityId($activity_id);
    }

    public function calculateColor(): string
    {
        $config = $this->activityPlan()->with('activity')->first();
        
        if (!$config) return 'negative';

        $activity = $config->activity;
        $goalType = trim($activity->goal_type);
        $dailyGoal = $config->daily_goal;

        // 1. Lógica para Dominio
        if ($goalType == 'Dominio') {
            return match ($this->score) {
                'dominada' => 'positive',
                'presentada', 'en proceso' => 'warning',
                default => 'negative',
            };
        }

        // 2. Lógica para Normal
        if ($goalType == 'Normal') {
            if (!$dailyGoal || $dailyGoal == 0) return 'negative';
            
            $rate = ($this->score / intval($dailyGoal)) * 100;
            
            if ($rate >= 66.67) return 'positive';
            if ($rate >= 33.34) return 'warning';
            return 'negative';
        }

        // 3. Lógica para Incremental o Acumulada
        if ($goalType == 'Incremental' || $goalType == 'Acumulada') {
            // Buscamos el valor anterior basado en la nueva estructura
            $prevScore = self::where('candidate_id', $this->candidate_id)
                ->where('activity_plan_id', $this->activity_plan_id)
                ->where('id', '<', $this->id ?? PHP_INT_MAX) // Maneja creación y edición
                ->orderBy('id', 'desc')
                ->value('score') ?? 0;

            if (is_null($prevScore)) return 'positive';
            if ($this->score > $prevScore) return 'positive';
            if ($this->score == $prevScore) return 'warning';
            
            return 'negative';
        }

        return 'negative';
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->color = $model->calculateColor();
        });

        static::updating(function ($model) {
            if ($model->isDirty(['score', 'activity_plan_id'])) {
                $model->color = $model->calculateColor();
            }
        });
    }

    public function activityPlan(){
        return $this->belongsTo(ActivityPlan::class);
    }
}
