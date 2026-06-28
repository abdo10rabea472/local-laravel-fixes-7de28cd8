<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = ['name','email','phone','subject','message','status','ip'];

    public function scopeNew($q) { return $q->where('status','new'); }
}
