<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    protected $fillable = [
        'blog_category_id','author_id','title','slug','image',
        'excerpt','content','views','published_at',
        'meta_title','meta_description','meta_keywords','og_image','canonical_url','no_index',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'no_index' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function ($p) {
            if (empty($p->slug)) $p->slug = Str::slug($p->title);
        });
    }

    public function category(): BelongsTo
    {
        // Now points to product Category table (shared taxonomy).
        return $this->belongsTo(Category::class, 'blog_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'author_id');
    }

    public function scopePublished($q)
    {
        return $q->whereNotNull('published_at')->where('published_at', '<=', now());
    }
}
