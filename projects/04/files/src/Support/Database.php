<?php

namespace App\Support;

class Database
{
    public static function pdo(): \PDO
    {
        // Read required settings from environment variables (no hardcoded defaults)
        $get = static function (string $key): ?string {
            return $_ENV[$key] ?? $_SERVER[$key] ?? (getenv($key) !== false ? getenv($key) : null);
        };

        $host    = $get('DB_HOST');
        $name    = $get('DB_NAME');
        $user    = $get('DB_USER');
        $pass    = $get('DB_PASS');
        $charset = $get('DB_CHARSET');

        if ($host === null || $name === null || $user === null || $pass === null || $charset === null) {
            throw new \RuntimeException('Missing one or more DB_* environment variables. Ensure Dotenv is installed, .env exists at project root, and keys DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_CHARSET are set.');
        }

        // Normalize charset to lowercase (e.g., UTF8 -> utf8)
        $charset = strtolower($charset);

        $dsn = "mysql:host={$host};dbname={$name};charset={$charset}";

        return new \PDO($dsn, $user, $pass, [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
}
