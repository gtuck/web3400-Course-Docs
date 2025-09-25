<?php
// filepath: config.php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$siteName = $siteName ?? 'Site';

// Use env vars or defaults for local dev
$dsn  = $_ENV['DB_DSN']  ?? 'mysql:host=127.0.0.1;dbname=web3400;charset=utf8mb4';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? '';

$options = [
  PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES   => false,
];

$pdo = new PDO($dsn, $user, $pass, $options);

// Flash helpers
function flash(string $msg, string $type = 'success'): void {
  $_SESSION['flash'][] = ['type' => $type, 'msg' => $msg];
}
function get_flashes(): array {
  $f = $_SESSION['flash'] ?? [];
  unset($_SESSION['flash']);
  return $f;
}

// Slugify helper
function slugify(string $text): string {
  $text = trim($text);
  if (function_exists('iconv')) {
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
  }
  $text = preg_replace('~[^\pL\d]+~u', '-', $text);
  $text = trim($text, '-');
  $text = strtolower($text);
  $text = preg_replace('~[^-a-z0-9]+~', '', $text);
  return $text ?: 'post';
}
