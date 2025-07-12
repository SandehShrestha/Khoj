<?php
require_once __DIR__ . '/config/database.php';

echo "<h2>Database Connection Test</h2>";

try {
    $pdo = getDBConnection();
    echo "<p style='color: green;'>✓ Database connection successful!</p>";
    
    // Test if we can query the User table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM User");
    $result = $stmt->fetch();
    echo "<p>Total users in database: " . $result['count'] . "</p>";
    
    // Show the user you registered
    $stmt = $pdo->prepare("SELECT * FROM User WHERE Email = ?");
    $stmt->execute(['sandeshshre221@gmail.com']);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "<p style='color: green;'>✓ Found your registered user:</p>";
        echo "<ul>";
        echo "<li>Username: " . htmlspecialchars($user['Username']) . "</li>";
        echo "<li>Email: " . htmlspecialchars($user['Email']) . "</li>";
        echo "<li>Phone: " . htmlspecialchars($user['Phone_Number']) . "</li>";
        echo "</ul>";
    } else {
        echo "<p style='color: red;'>✗ User not found in database</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>If connection works, try logging in at: <a href='login/index.php'>Login Page</a></li>";
echo "<li>If you still have issues, run: <a href='check_mysql_user.php'>MySQL User Check</a></li>";
echo "</ol>";
?> 