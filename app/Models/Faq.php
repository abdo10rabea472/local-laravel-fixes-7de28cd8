<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = ['category','question','answer','sort_order','active'];

    protected $casts = ['active' => 'boolean'];

    public function scopeActive($q) { return $q->where('active', true); }
}
