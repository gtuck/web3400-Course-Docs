<?php
// ---------- Site Variables ----------
$siteName     = "My PHP Site";
$contactEmail = "contact@example.com";
$contactPhone = "123-456-7890";

// ---------- Session & Flash Messages ----------
session_start();

if (!isset($_SESSION['messages'])) {
    $_SESSION['messages'] = [];
}
function flash($text, $type = 'is-info') {
    $_SESSION['messages'][] = ['type' => $type, 'text' => $text];
}

// ---------- Database Connection (PDO) ----------
try {
    $host = 'db';
    $dbname = 'web3400';
    $username = 'web3400';
    $password = 'password';

    $dsn = "mysql:host=$host;dbname=$dbname;charset=UTF8";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("Could not connect to database. Please try again later.");
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
