<?php
/**
 * User Model
 *
 * PURPOSE:
 * Represents user accounts in the system with authentication and role-based access control.
 * Extends BaseModel to inherit standard CRUD operations while adding user-specific methods.
 *
 * SECURITY MODEL:
 * - Mass assignment protection: $fillable excludes 'role' and 'active' to prevent privilege escalation
 * - Regular users can only update their name, email, and password via inherited update() method
 * - Admin-only methods (adminCreate, adminUpdate) explicitly handle role and active fields
 * - This prevents users from promoting themselves to admin or reactivating disabled accounts
 *
 * USAGE EXAMPLE:
 * ```php
 * // Regular user registration (role defaults to 'user', active defaults to 1)
 * $id = User::create([
 *     'name' => 'John Doe',
 *     'email' => 'john@example.com',
 *     'password_hash' => password_hash($password, PASSWORD_DEFAULT)
 * ]);
 *
 * // Admin creating a user with specific role
 * $id = User::adminCreate([
 *     'name' => 'Admin User',
 *     'email' => 'admin@example.com',
 *     'password_hash' => password_hash($password, PASSWORD_DEFAULT),
 *     'role' => 'admin',
 *     'active' => 1
 * ]);
 *
 * // Find user by email for login
 * $user = User::findByEmail('john@example.com');
 * if ($user && password_verify($password, $user['password_hash'])) {
 *     Auth::login($user);
 * }
 * ```
 */

namespace App\Models;

use App\Support\Database;
use PDO;

final class User extends BaseModel
{
    protected static string $table = 'users';

    /**
     * Mass-assignable fields
     *
     * SECURITY: 'role' and 'active' are intentionally excluded to prevent:
     * - Users promoting themselves to admin
     * - Disabled users reactivating their accounts
     * - Profile update forms accidentally changing roles
     *
     * Use adminCreate() and adminUpdate() for role/active changes
     */
    protected static array $fillable = ['name','email','password_hash'];

    /**
     * Find a user by their email address
     *
     * Commonly used during login to retrieve user credentials.
     *
     * @param string $email Email address to search for
     * @return array|null User data array or null if not found
     */
    public static function findByEmail(string $email): ?array
    {
        $sql = 'SELECT * FROM `users` WHERE `email` = :email LIMIT 1';
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Count active admin users
     *
     * Used to prevent removing the last admin from the system.
     *
     * @return int Number of active admin users
     */
    public static function countAdmins(): int
    {
        $stmt = Database::pdo()->query("SELECT COUNT(*) FROM `users` WHERE `role` = 'admin' AND `active` = 1");
        return (int)$stmt->fetchColumn();
    }

    /**
     * Create a new user with admin-level control (bypasses $fillable restrictions)
     *
     * ADMIN ONLY: This method allows setting role and active fields.
     * Should only be called from admin-protected controllers.
     *
     * @param array $data User data including name, email, password_hash, role, active
     * @return int The ID of the newly created user
     */
    public static function adminCreate(array $data): int
    {
        // Whitelist fields including role/active for admin actions
        $payload = [
            'name' => $data['name'] ?? null,
            'email' => $data['email'] ?? null,
            'password_hash' => $data['password_hash'] ?? null,
            'role' => $data['role'] ?? 'user',
            'active' => isset($data['active']) ? (int)$data['active'] : 1,
        ];
        $sql = 'INSERT INTO `users` (`name`,`email`,`password_hash`,`role`,`active`) VALUES (:name,:email,:password_hash,:role,:active)';
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($payload);
        return (int)Database::pdo()->lastInsertId();
    }

    /**
     * Update a user with admin-level control (bypasses $fillable restrictions)
     *
     * ADMIN ONLY: This method allows updating role and active fields.
     * Should only be called from admin-protected controllers.
     *
     * @param int $id User ID to update
     * @param array $data Fields to update (name, email, role, active)
     * @return bool True if update succeeded, false if no fields provided
     */
    public static function adminUpdate(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];
        foreach (['name','email','role','active'] as $col) {
            if (array_key_exists($col, $data)) {
                $fields[] = "`{$col}` = :{$col}";
                $params[':'.$col] = ($col === 'active') ? (int)$data[$col] : $data[$col];
            }
        }
        if (!$fields) return false;
        $sql = 'UPDATE `users` SET ' . implode(', ', $fields) . ' WHERE `id` = :id';
        $stmt = Database::pdo()->prepare($sql);
        return $stmt->execute($params);
    }
}
