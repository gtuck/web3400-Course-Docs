<?php
require '../vendor/autoload.php';

// Start session for flash messages and other session features
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load environment variables from project root
$dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();
$dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'DB_CHARSET'])->notEmpty();


$router = require '../src/Routes/index.php';
