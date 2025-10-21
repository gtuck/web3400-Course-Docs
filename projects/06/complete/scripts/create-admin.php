#!/usr/bin/env php
<?php
/**
 * Create an admin user via CLI.
 *
 * Usage:
 *   php scripts/create-admin.php "Admin Name" admin@example.com "PlaintextPassword" [role]
 *
 * Notes:
 * - Requires .env to be configured for DB connection.
 * - Creates the `users` table if it does not exist.
 * - Default role is 'admin'. Allowed roles: admin, editor, user
 */

declare(strict_types=1);

// Bootstrap Composer autoload
$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';

// Load environment
$dotenv = Dotenv\Dotenv::createImmutable($root);
$dotenv->safeLoad();

use App\Support\Database;

function out(string $msg): void { fwrite(STDOUT, $msg . PHP_EOL); }
function err(string $msg): void { fwrite(STDERR, "Error: {$msg}" . PHP_EOL); }

$argv = $_SERVER['argv'] ?? [];
array_shift($argv); // drop script name

if (count($argv) < 3) {
    out('Usage: php scripts/create-admin.php "Admin Name" admin@example.com "PlaintextPassword" [role]');
    exit(1);
}

[$name, $email, $password] = [$argv[0], strtolower(trim($argv[1])), $argv[2]];
$role = $argv[3] ?? 'admin';

$allowedRoles = ['admin','editor','user'];
if (!in_array($role, $allowedRoles, true)) {
    err("Invalid role '{$role}'. Allowed: " . implode(',', $allowedRoles));
    exit(1);
}

try {
    $pdo = Database::pdo();

    // Ensure table exists
    $pdo->exec(<<<SQL
CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','editor','user') NOT NULL DEFAULT 'user',
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX (email)
)
SQL);

    // Check if user exists
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $existing = $stmt->fetchColumn();
    if ($existing) {
        err("A user with email '{$email}' already exists (id={$existing}).");
        exit(1);
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (name,email,password_hash,role,active) VALUES (:name,:email,:hash,:role,1)');
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':hash' => $hash,
        ':role' => $role,
    ]);

    $id = (int)$pdo->lastInsertId();
    out("Created user #{$id} ({$email}) with role '{$role}'.");
    exit(0);
} catch (Throwable $e) {
    err($e->getMessage());
    exit(1);
}

