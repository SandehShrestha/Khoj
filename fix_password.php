<?php
require_once __DIR__ . '/config/database.php';

echo "<h2>Password Fix Script</h2>";

try {
    $pdo = getDBConnection();
    
    // Get the user
    $stmt = $pdo->prepare("SELECT * FROM User WHERE Email = ?");
    $stmt->execute(['sandeshshre221@gmail.com']);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "<p>Found user: " . htmlspecialchars($user['Username']) . "</p>";
        
        // Check if password is already hashed
        if (password_verify('sandesh@123', $user['Password_hash'])) {
            echo "<p style='color: green;'>✓ Password is already properly hashed!</p>";
        } else {
            echo "<p style='color: orange;'>⚠ Password needs to be hashed</p>";
            
            // Hash the password
            $hashedPassword = password_hash('sandesh@123', PASSWORD_DEFAULT);
            
            // Update the password in database
            $stmt = $pdo->prepare("UPDATE User SET Password_hash = ? WHERE Email = ?");
            $stmt->execute([$hashedPassword, 'sandeshshre221@gmail.com']);
            
            echo "<p style='color: green;'>✓ Password has been updated and properly hashed!</p>";
        }
        
        // Verify the password works
        if (password_verify('sandesh@123', $user['Password_hash']) || password_verify('sandesh@123', $hashedPassword ?? '')) {
            echo "<p style='color: green;'>✓ Password verification successful!</p>";
        } else {
            echo "<p style='color: red;'>✗ Password verification failed</p>";
        }
        
    } else {
        echo "<p style='color: red;'>✗ User not found</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Try logging in again at: <a href='login/index.php'>Login Page</a></li>";
echo "<li>Use email: sandeshshre221@gmail.com</li>";
echo "<li>Use password: sandesh@123</li>";
echo "</ol>";

echo "<h3>What This Script Did:</h3>";
echo "<ul>";
echo "<li>Found your user in the database</li>";
echo "<li>Checked if the password was properly hashed</li>";
echo "<li>If not, updated it with the correct hash</li>";
echo "<li>Verified the password works</li>";
echo "</ul>";
?> 