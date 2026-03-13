<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $appends = ['full_name', 'phones'];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    protected function rfc(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => strtoupper(str_replace(' ', '', $value)),
        );
    }

    public function getFullNameAttribute(){
        $fullNameArray = array_filter([$this->first_name, $this->last_name, $this->middle_name]);
        return join(" ", $fullNameArray);
    }

    public function getPhonesAttribute(){
        $phones = array_filter([$this->whatsapp, $this->home_phone]);
        return join(", ", $phones);
    }
}
