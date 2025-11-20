<?php

namespace App\Models;

class User extends BaseModel
{
    protected static string $table = 'users';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'name',
        'email',
        'password_hash',
        'role',
        'is_active',
    ];

    /**
     * Count users by role.
     */
    public static function countByRole(string $role): int
    {
        $sql = 'SELECT COUNT(*) FROM `' . static::table() . '` WHERE `role` = :role';
        $stmt = static::pdo()->prepare($sql);
        $stmt->bindValue(':role', $role);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }
}
