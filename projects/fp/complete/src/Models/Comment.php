<?php

namespace App\Models;

use PDO;

final class Comment extends BaseModel
{
    protected static string $table = 'comments';
    protected static string $primaryKey = 'id';
    protected static array $fillable = ['post_id', 'user_id', 'body', 'status'];

    /**
     * Get all published comments for a post, with user names.
     */
    public static function publishedForPost(int $postId): array
    {
        $pdo = static::pdo();
        $stmt = $pdo->prepare("
            SELECT c.*, u.name AS user_name
            FROM `comments` c
            JOIN `users` u ON u.id = c.user_id
            WHERE c.post_id = :post_id AND c.status = 'published'
            ORDER BY c.created_at ASC
        ");
        $stmt->bindValue(':post_id', $postId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get all distinct published posts a user has commented on.
     */
    public static function postsCommentedByUser(int $userId): array
    {
        $pdo = static::pdo();
        $stmt = $pdo->prepare("
            SELECT DISTINCT p.*
            FROM `comments` c
            JOIN `posts` p ON p.id = c.post_id
            WHERE c.user_id = :user_id
              AND c.status <> 'deleted'
              AND p.status = 'published'
            ORDER BY p.published_at DESC
        ");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get all comments with post and user details, with optional status filter.
     */
    public static function allWithDetails(?string $statusFilter = null, int $limit = 200): array
    {
        $sql = "
            SELECT c.*, p.title AS post_title, p.slug AS post_slug,
                   u.name AS user_name, u.email AS user_email
            FROM `comments` c
            JOIN `posts` p ON p.id = c.post_id
            JOIN `users` u ON u.id = c.user_id
        ";
        $params = [];
        if ($statusFilter && in_array($statusFilter, ['pending', 'published', 'deleted'], true)) {
            $sql .= " WHERE c.status = :status";
            $params[':status'] = $statusFilter;
        }
        $sql .= " ORDER BY c.created_at DESC LIMIT :limit";

        $pdo = static::pdo();
        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

