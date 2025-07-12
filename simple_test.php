<?php
echo "<h1>Simple PHP Test</h1>";
echo "<p>PHP is working!</p>";

// Test database connection
try {
    $pdo = new PDO("mysql:host=127.0.0.1;port=3307;dbname=lost_and_found_project", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✓ Database connection successful!</p>";
    
    // Test query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM User");
    $result = $stmt->fetch();
    echo "<p>Total users: " . $result['count'] . "</p>";
    
    // Show all users
    $stmt = $pdo->query("SELECT * FROM User");
    $users = $stmt->fetchAll();
    
    echo "<h3>All Users in Database:</h3>";
    foreach ($users as $user) {
        echo "<div style='border: 1px solid #ccc; margin: 10px; padding: 10px;'>";
        echo "<p><strong>ID:</strong> " . $user['User_ID'] . "</p>";
        echo "<p><strong>Username:</strong> " . htmlspecialchars($user['Username']) . "</p>";
        echo "<p><strong>Email:</strong> " . htmlspecialchars($user['Email']) . "</p>";
        echo "<p><strong>Phone:</strong> " . htmlspecialchars($user['Phone_Number']) . "</p>";
        echo "<p><strong>Password Hash:</strong> " . htmlspecialchars($user['Password_hash']) . "</p>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database error: " . $e->getMessage() . "</p>";
}

echo "<h3>Test Links:</h3>";
echo "<ul>";
echo "<li><a href='debug_login.php'>Debug Login</a></li>";
echo "<li><a href='signup/'>Signup Page</a></li>";
echo "<li><a href='login/'>Login Page</a></li>";
echo "</ul>";
?> 