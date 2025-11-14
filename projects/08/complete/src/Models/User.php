<?php

namespace App\Models;

class User extends BaseModel
{
    protected static string $table = 'users';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'name',
        'email',
        'password_hash',
        'role',
        'is_active',
    ];
}
