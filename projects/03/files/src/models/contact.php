<?php

class Contact
{
    public function getData(): array
    {
        $dsn = "mysql:host=localhost;dbname=contact_db;charset=utf8;port=3306";

        $pdo = new PDO($dsn, "contact_db_user", "secret", [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        $stmt = $pdo->query("SELECT * FROM contact_us");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
