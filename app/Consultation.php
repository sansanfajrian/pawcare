<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function userDoctorDetail()
    {
        return $this->belongsTo('App\UserDoctorDetail', 'user_doctor_detail_id');
    }

    public function payment()
    {
        return $this->hasOne('App\Payment');
    }

    public function review()
    {
        return $this->hasOne('App\Review');
    }
}
