<?php

namespace App\Models;

final class PostFavorite extends BaseModel
{
    protected static string $table = 'post_favorites';
    protected static string $primaryKey = 'id';
    protected static array $fillable = ['post_id', 'user_id'];
}

