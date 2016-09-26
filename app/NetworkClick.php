<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NetworkClick extends Model
{
    protected $fillable = [
        'network_id',
        'network_offer_id',
        'sub_id',
        'amount',
        'ip',
    ];

    public function network()
    {
        return $this->belongsTo(Network::class);
    }
}
