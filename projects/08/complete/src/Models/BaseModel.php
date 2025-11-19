<?php

namespace App\Models;

use App\Support\Database;
use PDO;

/**
 * BaseModel - Abstract base class for all database models
 *
 * Provides a simple Active Record-like pattern for database operations.
 * All model classes should extend this class and define their table name,
 * primary key, and fillable columns.
 *
 * Example usage:
 *
 * class User extends BaseModel {
 *     protected static string $table = 'users';
 *     protected static string $primaryKey = 'id';
 *     protected static array $fillable = ['name', 'email', 'password'];
 * }
 *
 * // Find a user by ID
 * $user = User::find(1);
 *
 * // Get all users
 * $users = User::all();
 *
 * // Create a new user
 * $id = User::create(['name' => 'John', 'email' => 'john@example.com']);
 *
 * // Update a user
 * User::update(1, ['name' => 'Jane']);
 *
 * // Delete a user
 * User::delete(1);
 */
abstract class BaseModel
{
    /** @var string Table name (override in subclass) */
    protected static string $table;

    /** @var string Primary key column name (default: 'id') */
    protected static string $primaryKey = 'id';

    /** @var array<string> Fillable columns for create/update operations (whitelist for mass assignment protection) */
    protected static array $fillable = [];

    /**
     * Get the PDO database connection instance
     *
     * @return PDO The database connection object
     *
     * Example:
     * $pdo = static::pdo();
     * $stmt = $pdo->prepare('SELECT * FROM users WHERE active = 1');
     */
    protected static function pdo(): PDO
    {
        return Database::pdo();
    }

    /**
     * Get the table name for this model
     *
     * @return string The database table name
     *
     * Example:
     * $tableName = static::table(); // Returns 'users' for User model
     */
    protected static function table(): string
    {
        return static::$table;
    }

    /**
     * Get the primary key column name for this model
     *
     * @return string The primary key column name (typically 'id')
     *
     * Example:
     * $pk = static::pk(); // Returns 'id' by default
     */
    protected static function pk(): string
    {
        return static::$primaryKey;
    }

    /**
     * Sanitize input data to only include fillable columns
     *
     * This provides mass assignment protection by filtering out any
     * columns that are not explicitly defined in the $fillable array.
     *
     * @param array $data Raw input data (e.g., from $_POST)
     * @return array Sanitized data containing only fillable columns
     *
     * Example:
     * // If $fillable = ['name', 'email']
     * $input = ['name' => 'John', 'email' => 'john@example.com', 'is_admin' => 1];
     * $safe = static::sanitize($input);
     * // Result: ['name' => 'John', 'email' => 'john@example.com']
     * // Note: 'is_admin' is removed because it's not in $fillable
     */
    protected static function sanitize(array $data): array
    {
        return array_intersect_key($data, array_flip(static::$fillable));
    }

    /**
     * Find a single record by its primary key
     *
     * Retrieves one row from the database matching the given ID.
     * Returns null if no record is found.
     *
     * @param int|string $id The primary key value to search for
     * @return array|null Associative array of the record, or null if not found
     *
     * Example:
     * // Find user with ID 5
     * $user = User::find(5);
     * if ($user) {
     *     echo $user['name']; // Access column values
     *     echo $user['email'];
     * } else {
     *     echo "User not found";
     * }
     *
     * // Also works with string IDs (e.g., UUIDs)
     * $item = Item::find('550e8400-e29b-41d4-a716-446655440000');
     */
    public static function find(int|string $id): ?array
    {
        $sql = 'SELECT * FROM `' . static::table() . '` WHERE `' . static::pk() . '` = :id LIMIT 1';
        $stmt = static::pdo()->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Retrieve all records from the table with pagination and ordering
     *
     * Fetches multiple records with support for limiting, offsetting, and custom ordering.
     * By default, returns up to 100 records ordered by primary key descending (newest first).
     *
     * SECURITY NOTE: If exposing $orderBy from user input, always whitelist allowed columns
     * in your calling code to prevent SQL injection.
     *
     * @param int $limit Maximum number of records to return (default: 100)
     * @param int $offset Number of records to skip (for pagination, default: 0)
     * @param string|null $orderBy Custom ORDER BY clause (default: primary key DESC)
     * @return array Array of associative arrays, each representing a record
     *
     * Example:
     * // Get the first 10 users (default newest first)
     * $users = User::all(10);
     *
     * // Get the next 10 users (pagination)
     * $users = User::all(10, 10); // Skip first 10, get next 10
     *
     * // Custom ordering by name ascending
     * $users = User::all(50, 0, '`name` ASC');
     *
     * // Page 3 of results (20 per page)
     * $page = 3;
     * $perPage = 20;
     * $offset = ($page - 1) * $perPage;
     * $users = User::all($perPage, $offset);
     *
     * // Iterate through results
     * foreach ($users as $user) {
     *     echo $user['name'] . '<br>';
     * }
     */
    public static function all(int $limit = 100, int $offset = 0, ?string $orderBy = null): array
    {
        $order = $orderBy ?: '`' . static::pk() . '` DESC';
        // If exposing $orderBy from user-input, whitelist columns in callers.
        $sql = 'SELECT * FROM `' . static::table() . '` ORDER BY ' . $order . ' LIMIT :limit OFFSET :offset';
        $stmt = static::pdo()->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Create a new record in the database
     *
     * Inserts a new row with the provided data. Only columns listed in the
     * $fillable array will be inserted (mass assignment protection).
     * Returns the auto-generated ID of the newly created record.
     *
     * @param array $data Associative array of column => value pairs to insert
     * @return int The ID of the newly created record (from lastInsertId)
     * @throws \InvalidArgumentException If no fillable fields are provided
     *
     * Example:
     * // Create a new user
     * $userId = User::create([
     *     'name' => 'John Doe',
     *     'email' => 'john@example.com',
     *     'password' => password_hash('secret123', PASSWORD_DEFAULT)
     * ]);
     * echo "New user created with ID: $userId";
     *
     * // Using data from a form submission
     * $newId = User::create($_POST); // Only fillable fields will be used
     *
     * // Handle potential errors
     * try {
     *     $productId = Product::create([
     *         'name' => 'Widget',
     *         'price' => 29.99,
     *         'stock' => 100
     *     ]);
     * } catch (\InvalidArgumentException $e) {
     *     echo "Error: " . $e->getMessage();
     * }
     */
    public static function create(array $data): int
    {
        $data = static::sanitize($data);
        if (!$data) {
            throw new \InvalidArgumentException('No fillable fields provided.');
        }
        $cols = array_keys($data);
        $placeholders = array_map(fn($c) => ':' . $c, $cols);
        $quotedCols = array_map(fn($c) => '`' . $c . '`', $cols);
        $sql = 'INSERT INTO `' . static::table() . '` (' . implode(',', $quotedCols) . ') VALUES (' . implode(',', $placeholders) . ')';
        
        $pdo = static::pdo(); //new line
        
        // old/bad code: $stmt = static::pdo()->prepare($sql);
        $stmt = $pdo->prepare($sql); // Corrected code
        foreach ($data as $c => $v) {
            $stmt->bindValue(':' . $c, $v);
        }
        $stmt->execute();
        // old/bad code: return (int) static::pdo()->lastInsertId();
        return (int) $pdo->lastInsertId(); // Corrected code
    }

    public static function create(array $data): int
    {
        $data = static::sanitize($data);
        if (!$data) {
            throw new \InvalidArgumentException('No fillable fields provided.');
        }

        $cols         = array_keys($data);
        $placeholders = array_map(fn($c) => ':' . $c, $cols);
        $quotedCols   = array_map(fn($c) => '`' . $c . '`', $cols);

        $sql = 'INSERT INTO `' . static::table() . '` (' . implode(',', $quotedCols) . ')
                VALUES (' . implode(',', $placeholders) . ')';

        // Get ONE PDO instance and reuse it
        $pdo = static::pdo();

        $stmt = $pdo->prepare($sql);
        foreach ($data as $c => $v) {
            $stmt->bindValue(':' . $c, $v);
        }

        $stmt->execute();

        return (int) $pdo->lastInsertId();
    }

    /**
     * Update an existing record by its primary key
     *
     * Updates the specified record with new data. Only columns listed in the
     * $fillable array will be updated (mass assignment protection).
     * Returns true if the update query executed successfully, false if no
     * fillable fields were provided.
     *
     * @param int|string $id The primary key value of the record to update
     * @param array $data Associative array of column => value pairs to update
     * @return bool True if update executed successfully, false if no fillable data
     *
     * Example:
     * // Update a user's email
     * $success = User::update(5, ['email' => 'newemail@example.com']);
     * if ($success) {
     *     echo "User updated successfully";
     * }
     *
     * // Update multiple columns
     * User::update(10, [
     *     'name' => 'Jane Smith',
     *     'email' => 'jane@example.com',
     *     'status' => 'active'
     * ]);
     *
     * // Update from form data (only fillable fields will be used)
     * $userId = 3;
     * User::update($userId, $_POST);
     *
     * // Partial updates are allowed
     * Product::update(15, ['stock' => 50]); // Only updates stock column
     *
     * // Check if update affected any rows (not supported by default, but can be extended)
     * $result = User::update(99, ['name' => 'New Name']);
     * // $result is true if query executed, even if no rows matched ID 99
     */
    public static function update(int|string $id, array $data): bool
    {
        $data = static::sanitize($data);
        if (!$data) {
            return false;
        }
        $sets = [];
        foreach (array_keys($data) as $c) {
            $sets[] = '`' . $c . '` = :' . $c;
        }
        $sql = 'UPDATE `' . static::table() . '` SET ' . implode(', ', $sets) . ' WHERE `' . static::pk() . '` = :_id';
        $stmt = static::pdo()->prepare($sql);
        foreach ($data as $c => $v) {
            $stmt->bindValue(':' . $c, $v);
        }
        $stmt->bindValue(':_id', $id);
        return $stmt->execute();
    }

    /**
     * Delete a record by its primary key
     *
     * Permanently removes the specified record from the database.
     * Returns true if the deletion query executed successfully.
     *
     * WARNING: This operation is irreversible. Consider implementing soft deletes
     * (e.g., a 'deleted_at' timestamp column) for recoverable deletions.
     *
     * @param int|string $id The primary key value of the record to delete
     * @return bool True if deletion executed successfully
     *
     * Example:
     * // Delete a user by ID
     * $success = User::delete(5);
     * if ($success) {
     *     echo "User deleted successfully";
     * } else {
     *     echo "Failed to delete user";
     * }
     *
     * // Delete with confirmation
     * $userId = 10;
     * $user = User::find($userId);
     * if ($user && confirm("Delete user {$user['name']}?")) {
     *     User::delete($userId);
     * }
     *
     * // Batch deletion (manual loop needed)
     * $idsToDelete = [1, 2, 3, 4, 5];
     * foreach ($idsToDelete as $id) {
     *     User::delete($id);
     * }
     *
     * // NOTE: The return value indicates query success, not whether
     * // a row was actually deleted. Returns true even if ID doesn't exist.
     * $result = User::delete(99999); // Returns true even if user 99999 doesn't exist
     */
    public static function delete(int|string $id): bool
    {
        $sql = 'DELETE FROM `' . static::table() . '` WHERE `' . static::pk() . '` = :id';
        $stmt = static::pdo()->prepare($sql);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }

    // Find the first row where `$column = $value` or return null
    public static function firstBy(string $column, mixed $value): ?array
    {
        $sql = 'SELECT * FROM `' . static::table() . '` WHERE `' . $column . '` = :v LIMIT 1';
        $stmt = static::pdo()->prepare($sql);
        $stmt->bindValue(':v', $value);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    // Check if any row exists for `$column = $value`, with optional id exclusion
    public static function existsBy(string $column, mixed $value, ?int $exceptId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM `' . static::table() . '` WHERE `' . $column . '` = :v';
        if ($exceptId !== null) {
            $sql .= ' AND `' . static::pk() . '` <> :id';
        }
        $stmt = static::pdo()->prepare($sql);
        $stmt->bindValue(':v', $value);
        if ($exceptId !== null) {
            $stmt->bindValue(':id', $exceptId);
        }
        $stmt->execute();
        return (bool) $stmt->fetchColumn();
    }
}
