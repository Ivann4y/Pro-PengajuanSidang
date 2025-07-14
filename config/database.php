<?php

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'sistem_sidang');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Database connection options
define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
]);

// Database table prefixes (if any)
define('DB_PREFIX', '');

// Connection timeout
define('DB_TIMEOUT', 30);

// Debug mode for database queries
define('DB_DEBUG', false); 