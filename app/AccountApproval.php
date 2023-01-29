<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountApproval extends Model
{

    public function requester()
    {
        return $this->belongsTo('App\User', 'requester_id', 'id');
    }

    public function approver()
    {
        $this->belongsTo('App\User', 'approver_id');
    }
}
