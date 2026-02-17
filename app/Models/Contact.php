<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $appends = ['full_name'];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function getFullNameAttribute(){
        $fullNameArray = array_filter([$this->first_name, $this->last_name, $this->middle_name]);
        return join(" ", $fullNameArray);
    }
}
