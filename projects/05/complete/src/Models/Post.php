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

final class Post extends BaseModel
{
    /** @var string Database table name */
    protected static string $table = 'posts';

    /** @var string Primary key column */
    protected static string $primaryKey = 'id';

    /** @var array<string> Columns allowed for mass assignment */
    protected static array $fillable = ['title', 'slug', 'body'];
}
