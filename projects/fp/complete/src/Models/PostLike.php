<?php

namespace App\Models;

final class PostLike extends BaseModel
{
    protected static string $table = 'post_likes';
    protected static string $primaryKey = 'id';
    protected static array $fillable = ['post_id', 'user_id'];
}

