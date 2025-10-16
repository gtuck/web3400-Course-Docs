<?php

namespace App\Models;

final class Contact extends BaseModel
{
    protected static string $table = 'contact_us';
    protected static string $primaryKey = 'id';
    protected static array $fillable = ['name', 'email', 'message'];
}

