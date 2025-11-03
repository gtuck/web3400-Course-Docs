<?php

namespace App\Support;

class Database
{
    public static function pdo(): \PDO
    {
        // Read all required settings from environment variables (no hardcoded defaults)
        $host    = $_ENV['DB_HOST'];
        $name    = $_ENV['DB_NAME'];
        $user    = $_ENV['DB_USER'];
        $pass    = $_ENV['DB_PASS'];
        $charset = $_ENV['DB_CHARSET'];

        $dsn = "mysql:host={$host};dbname={$name};charset={$charset}";

        return new \PDO($dsn, $user, $pass, [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
}
