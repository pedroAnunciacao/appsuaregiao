<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['name', 'level', 'parent_id', 'youtube_url'];

    public function parent() {
        return $this->belongsTo(Location::class, 'parent_id');
    }
}
