<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles;

    public function guardName(){
        return 'sanctum';
    }

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function candidates() {
        return $this->hasManyThrough(Candidate::class, EvaluationSchedule::class);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify((new ResetPasswordNotification($token))->locale('es'));
    }

    public function getFullNameAttribute(){
        $fullNameArray = array_filter([$this->name, $this->last_name, $this->second_last_name]);
        return join(" ", $fullNameArray);
    }

    public function work_area(){
        return $this->belongsTo(WorkArea::class);
    }

    public function leader(){
        return $this->belongsTo(User::class, 'leader_id');
    }
}
