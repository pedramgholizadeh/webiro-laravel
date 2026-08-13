<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'template',
        'seo_title',
        'seo_description',
        'status',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}