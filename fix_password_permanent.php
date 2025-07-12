<?php
require_once __DIR__ . '/config/database.php';

echo "<h2>Permanent Password Fix</h2>";

try {
    $pdo = getDBConnection();
    
    // Get the user
    $stmt = $pdo->prepare("SELECT * FROM User WHERE Email = ?");
    $stmt->execute(['sandeshshre221@gmail.com']);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "<p>Found user: " . htmlspecialchars($user['Username']) . "</p>";
        echo "<p>Current password hash: " . htmlspecialchars($user['Password_hash']) . "</p>";
        
        // Check if password is already properly hashed
        if (password_verify('sandesh@123', $user['Password_hash'])) {
            echo "<p style='color: green;'>✓ Password is already properly hashed!</p>";
        } else {
            echo "<p style='color: orange;'>⚠ Password needs to be hashed - fixing now...</p>";
            
            // Hash the password
            $hashedPassword = password_hash('sandesh@123', PASSWORD_DEFAULT);
            
            // Update the password in database
            $stmt = $pdo->prepare("UPDATE User SET Password_hash = ? WHERE Email = ?");
            $stmt->execute([$hashedPassword, 'sandeshshre221@gmail.com']);
            
            echo "<p style='color: green;'>✓ Password has been permanently updated in database!</p>";
            echo "<p>New hash: " . htmlspecialchars($hashedPassword) . "</p>";
            
            // Verify it works
            if (password_verify('sandesh@123', $hashedPassword)) {
                echo "<p style='color: green;'>✓ Password verification successful!</p>";
            } else {
                echo "<p style='color: red;'>✗ Password verification failed after update!</p>";
            }
        }
        
        // Test the login function
        echo "<h3>Testing Login Function:</h3>";
        require_once __DIR__ . '/includes/auth.php';
        
        $result = loginUser('sandeshshre221@gmail.com', 'sandesh@123');
        echo "<p>Login result: " . json_encode($result) . "</p>";
        
        if ($result['success']) {
            echo "<p style='color: green;'>✓ Login function works perfectly!</p>";
        } else {
            echo "<p style='color: red;'>✗ Login function failed: " . $result['message'] . "</p>";
        }
        
    } else {
        echo "<p style='color: red;'>✗ User not found!</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Try logging in at: <a href='login/index.php'>Login Page</a></li>";
echo "<li>Use email: sandeshshre221@gmail.com</li>";
echo "<li>Use password: sandesh@123</li>";
echo "</ol>";

echo "<h3>What This Script Did:</h3>";
echo "<ul>";
echo "<li>Found your user in the database</li>";
echo "<li>Checked if the password was properly hashed</li>";
echo "<li>If not, permanently updated the database with the correct hash</li>";
echo "<li>Tested the login function to make sure it works</li>";
echo "</ul>";
?> 