<?php
namespace App\Models;

final class Blog extends BaseModel
{
    protected static string $table = 'posts';
    protected static string $primaryKey = 'id';
    protected static array $fillable = array (
  0 => 'title',
  1 => 'slug',
  2 => 'body',
);
}
