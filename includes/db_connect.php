<?php
require_once __DIR__ . '/../config/db_config.php';

try {
    // Build DSN (Data Source Name)
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    
    // PDO options for security and error handling
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    // For Aiven SSL (if required)
    if (defined('DB_SSL_CA') && DB_SSL_CA) {
        $options[PDO::MYSQL_ATTR_SSL_CA] = DB_SSL_CA;
        $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
    }
    
    $conn = new PDO($dsn, DB_USER, DB_PASS, $options);
    
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>