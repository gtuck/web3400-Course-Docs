<?php

namespace App\Models;

use App\Support\Database;
use PDO;

abstract class BaseModel
{
    /** @var string Table name (override in subclass) */
    protected static string $table;
    /** @var string Primary key column name */
    protected static string $primaryKey = 'id';
    /** @var array<string> Fillable columns for create/update */
    protected static array $fillable = [];

    protected static function pdo(): PDO
    {
        return Database::pdo();
    }

    protected static function table(): string
    {
        return static::$table;
    }

    protected static function pk(): string
    {
        return static::$primaryKey;
    }

    protected static function sanitize(array $data): array
    {
        return array_intersect_key($data, array_flip(static::$fillable));
    }

    public static function find(int|string $id): ?array
    {
        $sql = 'SELECT * FROM `' . static::table() . '` WHERE `' . static::pk() . '` = :id LIMIT 1';
        $stmt = static::pdo()->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

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
        $stmt = static::pdo()->prepare($sql);
        foreach ($data as $c => $v) {
            $stmt->bindValue(':' . $c, $v);
        }
        $stmt->execute();
        return (int) static::pdo()->lastInsertId();
    }

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

    public static function delete(int|string $id): bool
    {
        $sql = 'DELETE FROM `' . static::table() . '` WHERE `' . static::pk() . '` = :id';
        $stmt = static::pdo()->prepare($sql);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }
}
