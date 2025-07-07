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

    public function sponsors(){
        return $this->belongsToMany(Sponsor::class, 'payment_configs');
    }

    public function payment_configs(){
        return $this->hasMany(PaymentConfig::class);
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

    public function scopeBeneficiaries(Builder $query){
        return $query->whereAcceptanceStatus(1);
    }

    public function scopeEvaluationBetween(Builder $query, $startDate, $endDate): Builder
    {
        if( !$startDate || !$endDate ){
            return $query;
        }

        $endDate = Carbon::parse($endDate)->endOfDay();
        // Subquery to find the most recent evaluation schedule ID for each candidate within the date range

        $mostRecentScheduleIds = Appointment::query()
            ->where('type_id', 0)
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

    public function appointments(){
        return $this->hasMany(Appointment::class);
    }

    public function payments(){
        return $this->hasMany(Payment::class);
    }

    public function getEvaluationScheduleAttribute(){
        return $this->appointments()
        ->where('type_id', 0)
        ->where('status', '!=', 'canceled')
        ->with('evaluator')
        ->orderBy('created_at', 'desc')
        ->first();
    }

    public function getEvaluationSchedulesAttribute(){
        return $this->appointments()
        ->where('type_id', 0)
        ->with('evaluator')
        ->orderBy('created_at', 'desc')
        ->get();
    }

    public function getFullNameAttribute(){
        $fullNameArray = array_filter([$this->first_name, $this->last_name, $this->middle_name]);
        return join(" ", $fullNameArray);
    }
}
