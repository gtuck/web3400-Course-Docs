<?php

namespace App\Models;

use PDO;

final class PostFavorite extends BaseModel
{
    protected static string $table = 'post_favorites';
    protected static string $primaryKey = 'id';
    protected static array $fillable = ['post_id', 'user_id'];

    /**
     * Check if a favorite exists for a given post and user.
     */
    public static function existsForUser(int $postId, int $userId): bool
    {
        $pdo = static::pdo();
        $stmt = $pdo->prepare('SELECT 1 FROM `post_favorites` WHERE `post_id` = :post_id AND `user_id` = :user_id LIMIT 1');
        $stmt->bindValue(':post_id', $postId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return (bool)$stmt->fetchColumn();
    }

    /**
     * Delete a favorite for a given post and user.
     * Returns true if a row was deleted, false otherwise.
     */
    public static function deleteForUser(int $postId, int $userId): bool
    {
        $pdo = static::pdo();
        $stmt = $pdo->prepare('DELETE FROM `post_favorites` WHERE `post_id` = :post_id AND `user_id` = :user_id');
        $stmt->bindValue(':post_id', $postId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Get all published posts favorited by a user.
     */
    public static function postsFavoritedByUser(int $userId): array
    {
        $pdo = static::pdo();
        $stmt = $pdo->prepare("
            SELECT p.*
            FROM `post_favorites` pf
            JOIN `posts` p ON p.id = pf.post_id
            WHERE pf.user_id = :user_id
              AND p.status = 'published'
            ORDER BY p.published_at DESC
        ");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

