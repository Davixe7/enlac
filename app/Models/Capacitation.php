<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Capacitation extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Relación con los invitados internos (Usuarios).
     */
    public function internalGuests(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'capacitation_user');
    }

    /**
     * Relación con los invitados externos (Contactos).
     */
    public function externalGuests(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class, 'capacitation_contact');
    }
}
