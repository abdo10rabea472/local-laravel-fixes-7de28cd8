<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    protected $fillable = ['locale', 'group', 'key', 'value'];

    protected static function booted(): void
    {
        $clear = function (self $t) {
            \Illuminate\Support\Facades\Cache::forget("translations:{$t->locale}:{$t->group}");
        };
        static::saved($clear);
        static::deleted($clear);
    }
}
