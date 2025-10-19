<?php
/**
 * Blog Model
 *
 * PURPOSE:
 * Represents blog posts in the 'posts' database table.
 * Provides CRUD operations for blog post management.
 *
 * DATABASE TABLE: posts
 * PRIMARY KEY: id
 *
 * FILLABLE FIELDS:
 * - title: Post title
 * - slug: URL-friendly identifier
 * - body: Post content
 *
 * USAGE EXAMPLES:
 *
 * // Create a new blog post
 * $id = Blog::create([
 *     'title' => 'Getting Started with PHP',
 *     'slug' => 'getting-started-with-php',
 *     'body' => 'PHP is a powerful server-side scripting language...'
 * ]);
 *
 * // Find a specific post
 * $post = Blog::find(1);
 * echo $post['title'];
 *
 * // Get all posts ordered by date
 * $posts = Blog::all(limit: 10, orderBy: 'created_at DESC');
 *
 * // Update a post
 * Blog::update(1, [
 *     'title' => 'Updated Title',
 *     'body' => 'Updated content'
 * ]);
 *
 * // Delete a post
 * Blog::delete(1);
 */

namespace App\Models;

final class Blog extends BaseModel
{
    /** @var string Database table name */
    protected static string $table = 'posts';

    /** @var string Primary key column */
    protected static string $primaryKey = 'id';

    /** @var array<string> Columns allowed for mass assignment */
    protected static array $fillable = ['title', 'slug', 'body'];
}
