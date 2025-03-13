<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Candidate extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    protected $guarded = [];

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

    public function medications(){
        return $this->hasMany(Medication::class);
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
        $fullNameArray = array_filter([$this->first_name, $this->middle_name, $this->last_name]);
        return join(" ", $fullNameArray);
    }
}
