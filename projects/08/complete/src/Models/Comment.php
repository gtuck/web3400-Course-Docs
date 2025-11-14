<?php

namespace App\Models;

final class Comment extends BaseModel
{
    protected static string $table = 'comments';
    protected static string $primaryKey = 'id';
    protected static array $fillable = ['post_id', 'user_id', 'body', 'status'];
}

