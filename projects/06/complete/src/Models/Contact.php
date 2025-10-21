<?php
/**
 * Contact Model
 *
 * PURPOSE:
 * Represents contact form submissions in the 'contact_us' database table.
 * Stores messages from website visitors.
 *
 * DATABASE TABLE: contact_us
 * PRIMARY KEY: id
 *
 * FILLABLE FIELDS:
 * - name: Sender's name
 * - email: Sender's email address
 * - message: Message content
 *
 * USAGE EXAMPLES:
 *
 * // Create a new contact submission
 * $id = Contact::create([
 *     'name' => 'John Doe',
 *     'email' => 'john@example.com',
 *     'message' => 'I have a question about your services...'
 * ]);
 *
 * // Find a specific submission
 * $contact = Contact::find(1);
 * echo $contact['email'];
 *
 * // Get all recent submissions
 * $submissions = Contact::all(limit: 20, orderBy: 'created_at DESC');
 *
 * // Delete a submission
 * Contact::delete(1);
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
