<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function region()
    {

        return $this->belongsTo(Region::class);
    }

    public function emengencies()
    {
        return $this->hasMany(Emergency::class);
    }
    public function data()
    {
        return $this->hasMany(Data::class);
    }
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function role()
    {
        return $this->hasOne(Role::class);
    }
}
