<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{

    protected $fillable = [
        'title',
        'category_id',
        'desc',
        'content',
        'image',
        'status',
        'views'
    ];

    protected $dates = ['created_at', 'updated_at'];

    /**
     * post belong to one category.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function scopePublish($query)
    {
        $query->where('status', true);
    }

}
