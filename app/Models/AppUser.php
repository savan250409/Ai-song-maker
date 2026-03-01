<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppUser extends Model
{
    protected $fillable = [
        'api_user_id',
        'username',
        'password',
    ];

    public function songs()
    {
        return $this->hasMany(Song::class);
    }
}
