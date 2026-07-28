<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'name',
        'code',
        'is_private',
        'password',
        'owner_id'
    ];

    public static function generateCode(): string
    {
        return strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
    }
}
