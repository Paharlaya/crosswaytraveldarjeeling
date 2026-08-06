<?php
// test-db.php - Test database connection
require_once 'config/database.php';

echo "<h1>Database Connection Test</h1>";

try {
    $db = Database::getInstance()->getConnection();
    echo "<p style='color: green;'>✅ Database connected successfully!</p>";
    
    // Test query
    $result = $db->query("SHOW TABLES");
    echo "<h3>Tables in database:</h3>";
    echo "<ul>";
    while($row = $result->fetch(PDO::FETCH_NUM)) {
        echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul>";
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
}
?>