<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SongCover extends Model
{
    protected $fillable = ['voice_id', 'voice_name', 'image', 'tts_only'];
}
