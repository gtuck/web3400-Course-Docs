<?php
/*
  Project Configuration (shared on all pages)
  - Defines site variables
  - Starts the session and provides a flash() helper for notifications
  - Creates a PDO connection for database access
  - Provides a slugify() helper for generating URL-friendly slugs
*/

// ---------- Site Variables ----------
$siteName     = "My PHP Site";
$contactEmail = "contact@example.com";
$contactPhone = "123-456-7890";

// ---------- Session & Flash Messages ----------
session_start();

if (!isset($_SESSION['messages'])) {
    $_SESSION['messages'] = [];
}
/**
 * Add a flash notification message to be rendered on the next request.
 * Use Bulma types: is-info (default), is-success, is-warning, is-danger.
 */
function flash($text, $type = 'is-info') {
    $_SESSION['messages'][] = ['type' => $type, 'text' => $text];
}

// ---------- Database Connection (PDO) ----------
try {
    // Local dev defaults; change via environment or Docker compose as needed
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
    // Avoid exposing sensitive details in production
    die("Could not connect to database. Please try again later.");
}

/**
 * Convert a string to a URL-friendly slug.
 * Example: "Hello World!" → "hello-world"
 */
function slugify(string $text): string {
  $text = trim($text);
  if (function_exists('iconv')) {
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
  }
  // Replace non letters/digits with hyphens
  $text = preg_replace('~[^\pL\d]+~u', '-', $text);
  $text = trim($text, '-');
  $text = strtolower($text);
  // Remove any remaining invalid chars
  $text = preg_replace('~[^-a-z0-9]+~', '', $text);
  return $text ?: 'post';
}
