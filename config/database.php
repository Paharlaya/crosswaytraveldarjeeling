<?php
// config/database.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'crossway_travel');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Base URL - change this to your domain
define('BASE_URL', 'http://localhost/crossway/');
define('ADMIN_URL', BASE_URL . 'admin/');
?>