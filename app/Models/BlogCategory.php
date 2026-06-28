<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class BlogCategory extends Model
{
    protected $fillable = ['name','slug','description'];

    protected static function booted(): void
    {
        static::saving(function ($c) {
            if (empty($c->slug)) $c->slug = Str::slug($c->name);
        });
    }

    public function posts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }
}
