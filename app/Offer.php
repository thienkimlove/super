<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $fillable = [
        'name',
        'redirect_link',
        'click_rate',
        'geo_locations',
        'allow_devices',
        'network_id',
        'net_offer_id',
        'image',
        'status',
        'auto',
        'allow_multi_lead',
        'check_click_in_network'
    ];

    public function clicks()
    {
        return $this->hasMany(Click::class);
    }

    public function network()
    {
        return $this->belongsTo(Network::class);
    }

    public function getCountBackAttribute()
    {
        return NetworkClick::where('net_offer_id', $this->net_offer_id)->count();
    }
}
