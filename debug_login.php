<?php
require_once __DIR__ . '/config/database.php';

echo "<h2>Login Debug Script</h2>";

try {
    $pdo = getDBConnection();
    
    // Get the user and show all details
    $stmt = $pdo->prepare("SELECT * FROM User WHERE Email = ?");
    $stmt->execute(['sandeshshre221@gmail.com']);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "<h3>User Details:</h3>";
        echo "<ul>";
        echo "<li><strong>User ID:</strong> " . $user['User_ID'] . "</li>";
        echo "<li><strong>Username:</strong> " . htmlspecialchars($user['Username']) . "</li>";
        echo "<li><strong>Email:</strong> " . htmlspecialchars($user['Email']) . "</li>";
        echo "<li><strong>Phone:</strong> " . htmlspecialchars($user['Phone_Number']) . "</li>";
        echo "<li><strong>Password Hash:</strong> " . htmlspecialchars($user['Password_hash']) . "</li>";
        echo "<li><strong>Created At:</strong> " . $user['Created_At'] . "</li>";
        echo "</ul>";
        
        // Test password verification
        echo "<h3>Password Verification Test:</h3>";
        $testPassword = 'sandesh@123';
        
        echo "<p>Testing password: <strong>$testPassword</strong></p>";
        
        if (password_verify($testPassword, $user['Password_hash'])) {
            echo "<p style='color: green;'>✓ Password verification SUCCESSFUL!</p>";
        } else {
            echo "<p style='color: red;'>✗ Password verification FAILED!</p>";
            
            // Let's try to fix it
            echo "<h3>Fixing Password:</h3>";
            $newHash = password_hash($testPassword, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("UPDATE User SET Password_hash = ? WHERE Email = ?");
            $stmt->execute([$newHash, 'sandeshshre221@gmail.com']);
            
            echo "<p>Updated password hash to: " . htmlspecialchars($newHash) . "</p>";
            
            // Test again
            if (password_verify($testPassword, $newHash)) {
                echo "<p style='color: green;'>✓ Password verification now SUCCESSFUL!</p>";
            } else {
                echo "<p style='color: red;'>✗ Still failed after update!</p>";
            }
        }
        
    } else {
        echo "<p style='color: red;'>✗ User not found!</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

echo "<h3>Test Login Function:</h3>";
echo "<p>Now let's test the actual login function:</p>";

// Test the login function
require_once __DIR__ . '/includes/auth.php';

$result = loginUser('sandeshshre221@gmail.com', 'sandesh@123');
echo "<p>Login result: " . json_encode($result) . "</p>";

if ($result['success']) {
    echo "<p style='color: green;'>✓ Login function works!</p>";
} else {
    echo "<p style='color: red;'>✗ Login function failed: " . $result['message'] . "</p>";
}
?> 