<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VirtualLog extends Model
{
    protected $fillable = [
        'click_id',
        'network_click_id',
        'offer_id',
        'user_agent',
        'user_country',
        'response'
    ];
}
