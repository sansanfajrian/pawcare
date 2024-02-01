<?php

namespace App;

use MongoDB\Laravel\Eloquent\Model;


class Review extends Model
{

    public function consultation()
    {
        return $this->belongsTo('App\Consultation', 'consultation_id');
    }

}
