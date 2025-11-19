<?php

namespace App\Models;

use PDO;

final class PostLike extends BaseModel
{
    protected static string $table = 'post_likes';
    protected static string $primaryKey = 'id';
    protected static array $fillable = ['post_id', 'user_id'];

    /**
     * Check if a like exists for a given post and user.
     */
    public static function existsForUser(int $postId, int $userId): bool
    {
        $pdo = static::pdo();
        $stmt = $pdo->prepare('SELECT 1 FROM `post_likes` WHERE `post_id` = :post_id AND `user_id` = :user_id LIMIT 1');
        $stmt->bindValue(':post_id', $postId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return (bool)$stmt->fetchColumn();
    }

    /**
     * Delete a like for a given post and user.
     * Returns true if a row was deleted, false otherwise.
     */
    public static function deleteForUser(int $postId, int $userId): bool
    {
        $pdo = static::pdo();
        $stmt = $pdo->prepare('DELETE FROM `post_likes` WHERE `post_id` = :post_id AND `user_id` = :user_id');
        $stmt->bindValue(':post_id', $postId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Get all published posts liked by a user.
     */
    public static function postsLikedByUser(int $userId): array
    {
        $pdo = static::pdo();
        $stmt = $pdo->prepare("
            SELECT p.*
            FROM `post_likes` pl
            JOIN `posts` p ON p.id = pl.post_id
            WHERE pl.user_id = :user_id
              AND p.status = 'published'
            ORDER BY p.published_at DESC
        ");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

