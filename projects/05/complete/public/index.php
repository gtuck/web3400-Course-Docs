<?php
require '../vendor/autoload.php';

// Load environment variables from project root
$dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();
$dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'DB_CHARSET', 'SITE_NAME', 'SITE_EMAIL', 'SITE_PHONE'])->notEmpty();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$router = require '../src/Routes/index.php';
