<?php

namespace App\Models;

final class Blog extends BaseModel
{
    // Assumes a `posts` table with columns like id, title, body
    protected static string $table = 'posts';
    protected static string $primaryKey = 'id';
    protected static array $fillable = ['title', 'body'];
}
