<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $filable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'content',
        'excerpt',
        'thumbnail',
        'status',
        'published_at'
    ];
}
