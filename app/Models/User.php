<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Hidden fields on arrays / JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casts.
     *
     * These are important for password expiry + force reset.
     */
    protected $casts = [
        'email_verified_at'   => 'datetime',
        'password'            => 'hashed',    // Laravel will hash automatically on assign
        'password_changed_at' => 'datetime',  // so ->lt() works
        'force_password_reset'=> 'boolean',
    ];
}