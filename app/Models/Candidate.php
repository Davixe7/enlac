<?php

namespace App\Models;

use App\Enums\CandidateStatus;
use App\Models\Scopes\BeneficiaryScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Candidate extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, BeneficiaryScopes;
    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:d/m/Y',
        'entry_date' => 'datetime:d/m/Y',
        'status'     => \App\Enums\CandidateStatus::class,
    ];

    public $appends = ['full_name'];

    public function candidateStatus()
    {
        return $this->belongsTo(\App\Models\CandidateStatus::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class)->withDefault(['name' => 'SIN PROGRAMA', 'price' => 0]);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class);
    }

    public function personal_groups()
    {
        return $this->belongsToMany(Group::class)->whereIsIndividual(1);
    }

    public function medication_logs()
    {
        return $this->hasManyThrough(MedicationLog::class, Medication::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function evaluation()
    {
        return $this->hasMany(Evaluation::class)->oldestOfMany();
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    public function brainFunctionRanks()
    {
        return $this->hasMany(BrainFunctionRank::class);
    }

    public function interviewee()
    {
        return $this->hasOne(Interviewee::class)->withDefault(fn() => ['name' => '', 'relationship' => '', 'legal_relationship' => '']);
    }

    public function medications()
    {
        return $this->hasMany(Medication::class);
    }

    public function sponsors()
    {
        return $this->belongsToMany(Sponsor::class, 'payment_configs');
    }

    public function payment_configs()
    {
        return $this->hasMany(PaymentConfig::class);
    }

    public function payment_confix()
    {
        return $this->hasMany(PaymentConfig::class);
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

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function evaluationSchedule()
    {
        return $this->hasOne(Appointment::class)
            ->latestOfMany()
            ->where('type_id', 0)
            ->where('status', '!=', 'canceled')
            ->with('evaluator')
            ->withDefault(["type_id" => 0]);
    }

    public function evaluationSchedules()
    {
        return $this->hasMany(Appointment::class)
            ->where('type_id', 0)
            ->with('evaluator')
            ->orderBy('created_at', 'desc');
    }

    public function locationDetail()
    {
        return $this->hasOne(CandidateLocation::class)->withDefault([
            'transport_address' => null,
            'transport_location_link' => null,
            'curp' => null
        ]);
    }

    public function rides()
    {
        return $this->hasMany(Ride::class);
    }

    public function todaysRide()
    {
        $date = now()->format('Y-m-d');
        return $this->hasOne(Ride::class)
            ->where('date', $date)
            ->where('type', 'rubio')
            ->withDefault([
                'date' => $date,
                'departure_time' => null,
                'return_time' => null,
                'comments' => null,
                'type' => 'rubio'
            ]);
    }

    public function getFullNameAttribute()
    {
        $fullNameArray = array_filter([$this->first_name, $this->last_name, $this->middle_name]);
        return join(" ", $fullNameArray);
    }

    public function plans()
    {
        return $this->hasManyThrough(Plan::class, Group::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function scores()
    {
        return $this->hasMany(ActivityDailyScore::class);
    }

    public function enlacResponsible()
    {
        return $this->hasOne(Contact::class)
            ->ofMany([
                'id' => 'min'
            ], function ($query) {
                $query->where('enlac_responsible', 1);
            })
            ->withDefault(function () {
                return [
                    'first_name' => 'N/A',
                    'middle_name' => '',
                    'last_name' => '',
                ];
            });
    }

    public function updateStatus(CandidateStatus $newStatus, ?string $comments = null, $date = null): void
    {
        DB::transaction(function () use ($newStatus, $comments, $date) {

            $this->update(['status' => $newStatus]);

            $this->statusLogs()->create([
                'status'     => $newStatus->value,
                'user_id'    => Auth::id(),       
                'comments'   => $comments,
                'created_at' => $date ?: now(),
            ]);
        });
    }

    public function statusLogs(){
        return $this->hasMany(CandidateStatusLog::class);
    }
}
