<?php
echo "<h2>MySQL User Configuration Check</h2>";

// Try to connect with root user
try {
    // Try connecting as root
    $pdo = new PDO("mysql:host=127.0.0.1;port=3307", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color: green;'>✓ Connected to MySQL as root</p>";
    
    // Check if database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE 'lost_and_found_project'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✓ Database 'lost_and_found_project' exists</p>";
        
        // Check if tables exist
        $pdo->exec("USE lost_and_found_project");
        $tables = ['User', 'Items', 'Claims', 'claim_verifications'];
        
        foreach ($tables as $table) {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "<p style='color: green;'>✓ Table '$table' exists</p>";
            } else {
                echo "<p style='color: red;'>✗ Table '$table' does not exist</p>";
            }
        }
        
    } else {
        echo "<p style='color: red;'>✗ Database 'lost_and_found_project' does not exist</p>";
        echo "<p>Please create the database using the SQL commands in the README.md file</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Root connection failed: " . $e->getMessage() . "</p>";
    echo "<p>You may need to:</p>";
    echo "<ol>";
    echo "<li>Check if MySQL is running on port 3307</li>";
    echo "<li>Verify root password (if any)</li>";
    echo "<li>Try connecting with different credentials</li>";
    echo "</ol>";
}

echo "<h3>Current Configuration:</h3>";
echo "<p>The application is configured to use:</p>";
echo "<ul>";
echo "<li><strong>Username:</strong> root</li>";
echo "<li><strong>Password:</strong> (empty)</li>";
echo "<li><strong>Host:</strong> 127.0.0.1</li>";
echo "<li><strong>Port:</strong> 3307</li>";
echo "<li><strong>Database:</strong> lost_and_found_project</li>";
echo "</ul>";

echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>If connection works, try logging in at: <a href='login/index.php'>Login Page</a></li>";
echo "<li>Test database connection: <a href='test_connection.php'>Test Connection</a></li>";
echo "</ol>";
?> 