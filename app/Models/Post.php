<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'body',
        'enabled',
        'user_id',
        'category_id',
        'image_id',
    ];

    protected $with = [
        'category',
        'image',
        'user'
    ];

    public function category() {
        return $this->belongsTo('App\Models\Category');
    }

    public function image() {
        return $this->belongsTo('App\Models\Images');
    }

    public function user() {
        return $this->belongsTo('App\Models\User');
    }
}
