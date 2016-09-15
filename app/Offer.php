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
        'status'
    ];

    public function clicks()
    {
        return $this->hasMany(Click::class);
    }
}
