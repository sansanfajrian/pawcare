<?php

namespace App;

use MongoDB\Laravel\Eloquent\Model;

class Role extends Model
{

    public function users()
    {
        return $this->hasMany('App\User');
    }
}
