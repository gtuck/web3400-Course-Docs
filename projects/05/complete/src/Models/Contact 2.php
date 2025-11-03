<?php
/**
 * ContactU Model
 *
 * PURPOSE:
 * Represents records in the 'contact_us' database table.
 * Provides CRUD operations via ActiveRecord pattern.
 *
 * DATABASE TABLE: contact_us
 * PRIMARY KEY: id
 *
 * USAGE EXAMPLES:
 *
 * // Create a new record
 * $id = ContactU::create([
 *     // Add your fields here
 * ]);
 *
 * // Find a specific record
 * $record = ContactU::find(1);
 *
 * // Get all records
 * $records = ContactU::all(limit: 10, orderBy: 'created_at DESC');
 *
 * // Update a record
 * ContactU::update(1, [
 *     // Updated fields
 * ]);
 *
 * // Delete a record
 * ContactU::delete(1);
 */

namespace App\Models;

final class Contact extends BaseModel
{
    /** @var string Database table name */
    protected static string $table = 'contact_us';

    /** @var string Primary key column */
    protected static string $primaryKey = 'id';

    /** @var array<string> Columns allowed for mass assignment */
    protected static array $fillable = ['name', 'email', 'message'];
}
