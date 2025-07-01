<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $guarded = ['id'];

    // Tell Eloquent to cast published_at into a Carbon instance:
    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * Scope only published posts.
     */
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}
