<?php
/**
 * Post Model
 *
 * PURPOSE:
 * Represents records in the 'posts' database table.
 * Provides CRUD operations via ActiveRecord pattern.
 *
 * DATABASE TABLE: posts
 * PRIMARY KEY: id
 *
 * USAGE EXAMPLES:
 *
 * // Create a new record
 * $id = Post::create([
 *     // Add your fields here
 * ]);
 *
 * // Find a specific record
 * $record = Post::find(1);
 *
 * // Get all records
 * $records = Post::all(limit: 10, orderBy: 'created_at DESC');
 *
 * // Update a record
 * Post::update(1, [
 *     // Updated fields
 * ]);
 *
 * // Delete a record
 * Post::delete(1);
 */

namespace App\Models;

use PDO;

final class Post extends BaseModel
{
    /** @var string Database table name */
    protected static string $table = 'posts';

    /** @var string Primary key column */
    protected static string $primaryKey = 'id';

    /** @var array<string> Columns allowed for mass assignment */
    protected static array $fillable = [
        'author_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'featured_image',
        'status',
        'published_at',
        'is_featured',
        'favs',
        'likes',
        'comments_count',
    ];

    /**
     * Return a post with author and optional engagement flags for a given user.
     */
    public static function findBySlugWithAuthorAndEngagement(string $slug, ?int $userId = null): ?array
    {
        $sql = "
            SELECT p.*, u.name AS author_name
            FROM `posts` p
            JOIN `users` u ON u.id = p.author_id
            WHERE p.slug = :slug
            LIMIT 1
        ";
        $stmt = static::pdo()->prepare($sql);
        $stmt->bindValue(':slug', $slug);
        $stmt->execute();
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }

        if ($userId !== null) {
            // Has the user liked this post?
            $sqlLike = "
                SELECT 1 FROM `post_likes`
                WHERE post_id = :post_id AND user_id = :user_id
                LIMIT 1
            ";
            $likeStmt = static::pdo()->prepare($sqlLike);
            $likeStmt->bindValue(':post_id', (int)$row['id'], PDO::PARAM_INT);
            $likeStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $likeStmt->execute();
            $row['is_liked_by_user'] = (bool)$likeStmt->fetchColumn();

            // Has the user favorited this post?
            $sqlFav = "
                SELECT 1 FROM `post_favorites`
                WHERE post_id = :post_id AND user_id = :user_id
                LIMIT 1
            ";
            $favStmt = static::pdo()->prepare($sqlFav);
            $favStmt->bindValue(':post_id', (int)$row['id'], PDO::PARAM_INT);
            $favStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $favStmt->execute();
            $row['is_favorited_by_user'] = (bool)$favStmt->fetchColumn();
        }

        return $row;
    }

    public static function incrementLikes(int $postId): void
    {
        $pdo = static::pdo();
        $stmt = $pdo->prepare("UPDATE `posts` SET `likes` = `likes` + 1 WHERE `id` = :id");
        $stmt->bindValue(':id', $postId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public static function decrementLikes(int $postId): void
    {
        $pdo = static::pdo();
        $stmt = $pdo->prepare("UPDATE `posts` SET `likes` = GREATEST(`likes` - 1, 0) WHERE `id` = :id");
        $stmt->bindValue(':id', $postId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public static function incrementFavs(int $postId): void
    {
        $pdo = static::pdo();
        $stmt = $pdo->prepare("UPDATE `posts` SET `favs` = `favs` + 1 WHERE `id` = :id");
        $stmt->bindValue(':id', $postId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public static function decrementFavs(int $postId): void
    {
        $pdo = static::pdo();
        $stmt = $pdo->prepare("UPDATE `posts` SET `favs` = GREATEST(`favs` - 1, 0) WHERE `id` = :id");
        $stmt->bindValue(':id', $postId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public static function incrementComments(int $postId): void
    {
        $pdo = static::pdo();
        $stmt = $pdo->prepare("UPDATE `posts` SET `comments_count` = `comments_count` + 1 WHERE `id` = :id");
        $stmt->bindValue(':id', $postId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public static function decrementComments(int $postId): void
    {
        $pdo = static::pdo();
        $stmt = $pdo->prepare("UPDATE `posts` SET `comments_count` = GREATEST(`comments_count` - 1, 0) WHERE `id` = :id");
        $stmt->bindValue(':id', $postId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public static function findBySlug(string $slug): ?array
    {
        return static::firstBy('slug', $slug);
    }

    /**
     * Return recent featured, published posts with author name.
     */
    public static function recentFeaturedWithAuthors(int $limit = 10): array
    {
        $sql = "
            SELECT p.*, u.name AS author_name
            FROM `posts` p
            JOIN `users` u ON u.id = p.author_id
            WHERE p.status = 'published' AND p.is_featured = 1 AND p.published_at IS NOT NULL
            ORDER BY p.published_at DESC
            LIMIT :limit
        ";
        $stmt = static::pdo()->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function findBySlugWithAuthor(string $slug): ?array
    {
        $sql = "
            SELECT p.*, u.name AS author_name
            FROM `posts` p
            JOIN `users` u ON u.id = p.author_id
            WHERE p.slug = :slug
            LIMIT 1
        ";
        $stmt = static::pdo()->prepare($sql);
        $stmt->bindValue(':slug', $slug);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Convenience alias used in the Project 08 description.
     */
    public static function withEngagementBySlug(string $slug, ?int $userId = null): ?array
    {
        return static::findBySlugWithAuthorAndEngagement($slug, $userId);
    }

    /**
     * Dashboard helpers
     */
    public static function countByStatus(string $status): int
    {
        $sql = 'SELECT COUNT(*) FROM `' . static::table() . '` WHERE `status` = :status';
        $stmt = static::pdo()->prepare($sql);
        $stmt->bindValue(':status', $status);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public static function countFeatured(): int
    {
        $sql = 'SELECT COUNT(*) FROM `' . static::table() . '` WHERE `is_featured` = 1 AND `status` = \'published\'';
        $stmt = static::pdo()->query($sql);
        return (int) $stmt->fetchColumn();
    }

    public static function averageLikes(): float
    {
        $sql = 'SELECT COALESCE(AVG(`likes`), 0) FROM `' . static::table() . '` WHERE `status` = \'published\'';
        $stmt = static::pdo()->query($sql);
        return (float) $stmt->fetchColumn();
    }

    public static function averageFavs(): float
    {
        $sql = 'SELECT COALESCE(AVG(`favs`), 0) FROM `' . static::table() . '` WHERE `status` = \'published\'';
        $stmt = static::pdo()->query($sql);
        return (float) $stmt->fetchColumn();
    }

    public static function averageComments(): float
    {
        $sql = 'SELECT COALESCE(AVG(`comments_count`), 0) FROM `' . static::table() . '` WHERE `status` = \'published\'';
        $stmt = static::pdo()->query($sql);
        return (float) $stmt->fetchColumn();
    }
}
