<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use MongoDB\Laravel\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','email', 'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    public function role()
    {
        return $this->belongsTo('App\Role');
    }

    public function userDoctorDetails()
    {
        return $this->hasOne('App\UserDoctorDetail');
    }

    public function consultations()
    {
        return $this->hasMany('App\Consultation');
    }

    public function scopeAuthors($query)
    {
        return $query->where('role_id',2);
    }
}
