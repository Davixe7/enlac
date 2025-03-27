<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Candidate extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:d/m/Y',
        // ... otros casts
    ];

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function evaluation()
    {
        return $this->hasOne(Evaluation::class);
    }

    public function brainFunctionRanks() {
        return $this->hasMany(BrainFunctionRank::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function interviewee()
    {
        return $this->hasOne(Interviewee::class)->withDefault(fn()=>['name'=>'', 'relationship'=> '', 'legal_relationship'=> '']);
    }

    public function medications(){
        return $this->hasMany(Medication::class);
    }

    public function scopeBirthDate(Builder $query, $birthDate): Builder
    {
        return $birthDate ? $query->where('birth_date', $birthDate) : $query;
    }

    public function scopeName(Builder $query, $name): Builder
    {
        return $name ?
            $query->where(function (Builder $q) use ($name) {
            $q->where('first_name', 'like', '%' . $name . '%')
              ->orWhere('middle_name', 'like', '%' . $name . '%')
              ->orWhere('last_name', 'like', '%' . $name . '%');})
            : $query;
    }

    public function scopeEvaluationBetween(Builder $query, $startDate, $endDate): Builder
    {
        if( !$startDate || !$endDate ){
            return $query;
        }
        // Subquery to find the most recent evaluation schedule ID for each candidate within the date range

        $mostRecentScheduleIds = EvaluationSchedule::query()
            ->whereBetween('date', [$startDate, $endDate])
            ->select('candidate_id', DB::raw('MAX(id) as most_recent_id'))
            ->groupBy('candidate_id');

        return $query->joinSub($mostRecentScheduleIds, 'most_recent_schedules', function ($join) {
                $join->on('candidates.id', '=', 'most_recent_schedules.candidate_id');
            });
    }

    public function registerMediaConversions(?Media $media = null): void
    {
            $this->addMediaConversion('thumb')
                ->width(300)
                ->height(300);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile_picture')
            ->singleFile()
            ->useDisk('public');
    }

    public function evaluation_schedules(){
        return $this->hasMany(EvaluationSchedule::class);
    }

    public function getEvaluationScheduleAttribute(){
        return $this->evaluation_schedules()
        ->orderBy('created_at', 'desc')
        ->where('status', '!=', 'canceled')
        ->with('evaluator')
        ->first();
    }

    public function getFullNameAttribute(){
        $fullNameArray = array_filter([$this->first_name, $this->last_name, $this->middle_name]);
        return join(" ", $fullNameArray);
    }
}
