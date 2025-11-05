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
}
