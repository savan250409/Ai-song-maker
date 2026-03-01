<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    protected $fillable = [
        'app_user_id',
        'genre',
        'mood',
        'lyrics',
        'title',
        'song_url',
    ];

    public function appUser()
    {
        return $this->belongsTo(AppUser::class);
    }
}
