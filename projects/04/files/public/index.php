<?php

// Load Composer autoload (path relative to this file)
require dirname(__DIR__) . '/vendor/autoload.php';

// Load environment variables if phpdotenv is installed
if (class_exists(\Dotenv\Dotenv::class)) {
    $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->safeLoad();
    // Enforce required keys when available
    if (method_exists($dotenv, 'required')) {
        $dotenv->required(['DB_HOST','DB_NAME','DB_USER','DB_PASS','DB_CHARSET'])->notEmpty();
    }
}

require dirname(__DIR__) . '/src/Routes/index.php';
