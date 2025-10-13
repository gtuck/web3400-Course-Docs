<?php

class Blog
{
    public function getPosts(): array
    {
        // Database connection
        $host = 'db';
        $dbname = 'web3400';
        $username = 'web3400';
        $password = 'password';

        $dsn = "mysql:host=$host;dbname=$dbname;charset=UTF8";
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $stmt = $pdo->query("SELECT * FROM posts");

        return $stmt->fetchAll();
    }
}