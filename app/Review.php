<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    public function consultation()
    {
        return $this->belongsTo('App\Consultation', 'consultation_id');
    }

}
