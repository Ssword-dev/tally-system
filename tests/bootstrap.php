<?php

// Load environment variables if .env exists
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->safeLoad();
}

// Load the autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap the application
if (file_exists(__DIR__ . '/../src/bootstrap.php')) {
    require_once __DIR__ . '/../src/bootstrap.php';
}

// Test database setup (if needed)
// You can add test database setup here
