<?php
require_once __DIR__ . '/config/database.php';

echo "<h2>Database Connection Test</h2>";

// Test database connection
if (testConnection()) {
    echo "<p style='color: green;'>✓ Database connection successful!</p>";
    
    try {
        $pdo = getDBConnection();
        
        // Test if tables exist
        $tables = ['User', 'Items', 'Claims', 'claim_verifications'];
        
        foreach ($tables as $table) {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "<p style='color: green;'>✓ Table '$table' exists</p>";
            } else {
                echo "<p style='color: red;'>✗ Table '$table' does not exist</p>";
            }
        }
        
        // Test user count
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM User");
        $result = $stmt->fetch();
        echo "<p>Total users in database: " . $result['count'] . "</p>";
        
        // Test items count
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM Items");
        $result = $stmt->fetch();
        echo "<p>Total items in database: " . $result['count'] . "</p>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
    
} else {
    echo "<p style='color: red;'>✗ Database connection failed!</p>";
    echo "<p>Please check your database configuration in config/database.php</p>";
}
?>

<h3>Next Steps:</h3>
<ol>
    <li>Make sure your XAMPP Apache and MySQL services are running</li>
    <li>Verify your database credentials in config/database.php</li>
    <li>Access your application at: <a href="http://localhost/ItemHunt/">http://localhost/ItemHunt/</a></li>
    <li>Start with the signup page: <a href="http://localhost/ItemHunt/signup/">http://localhost/ItemHunt/signup/</a></li>
</ol> 