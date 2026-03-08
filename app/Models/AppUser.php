<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppUser extends Model
{
    protected $fillable = [
        'api_user_id',
        'username',
        'email_address',
        'password',
        'user_profile',
    ];

    public function songs()
    {
        return $this->hasMany(Song::class);
    }
}
