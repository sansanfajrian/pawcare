<?php

namespace App;

use MongoDB\Laravel\Eloquent\Model;

class UserDoctorDetail extends Model
{

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function consultations()
    {
        return $this->hasMany('App\Consultation');
    }
}

