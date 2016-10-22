<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MediaOffer extends Model
{
    protected $fillable = ['offer_id', 'offer_name', 'offer_preview_link', 'offer_tracking_link'];
}
