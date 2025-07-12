<?php
require_once __DIR__ . '/includes/auth.php';

echo "<h2>Signup Debug Test</h2>";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>Form Submitted!</h3>";
    echo "<p>POST data received:</p>";
    echo "<ul>";
    foreach ($_POST as $key => $value) {
        echo "<li><strong>$key:</strong> " . htmlspecialchars($value) . "</li>";
    }
    echo "</ul>";
    
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    
    echo "<h3>Validation Results:</h3>";
    
    // Validation
    $errors = [];
    
    if (empty($username)) {
        $errors[] = 'Username is required';
        echo "<p style='color: red;'>✗ Username is empty</p>";
    } else {
        echo "<p style='color: green;'>✓ Username: $username</p>";
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required';
        echo "<p style='color: red;'>✗ Email is empty</p>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
        echo "<p style='color: red;'>✗ Invalid email format</p>";
    } else {
        echo "<p style='color: green;'>✓ Email: $email</p>";
    }
    
    if (empty($phone)) {
        $errors[] = 'Phone number is required';
        echo "<p style='color: red;'>✗ Phone is empty</p>";
    } else {
        echo "<p style='color: green;'>✓ Phone: $phone</p>";
    }
    
    if (empty($password)) {
        $errors[] = 'Password is required';
        echo "<p style='color: red;'>✗ Password is empty</p>";
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters';
        echo "<p style='color: red;'>✗ Password too short</p>";
    } else {
        echo "<p style='color: green;'>✓ Password length: " . strlen($password) . "</p>";
    }
    
    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match';
        echo "<p style='color: red;'>✗ Passwords don't match</p>";
    } else {
        echo "<p style='color: green;'>✓ Passwords match</p>";
    }
    
    if (empty($errors)) {
        echo "<h3>Attempting to Register User...</h3>";
        // Register user
        $result = registerUser($username, $email, $password, $phone);
        
        echo "<p>Registration result: " . json_encode($result) . "</p>";
        
        if ($result['success']) {
            echo "<p style='color: green;'>✓ Registration successful!</p>";
            echo "<p><a href='login/index.php'>Go to Login</a></p>";
        } else {
            echo "<p style='color: red;'>✗ Registration failed: " . $result['message'] . "</p>";
        }
    } else {
        echo "<h3>Validation Errors:</h3>";
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li style='color: red;'>$error</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<p>No form submission detected.</p>";
}

echo "<h3>Test Links:</h3>";
echo "<ul>";
echo "<li><a href='signup/'>Original Signup Page</a></li>";
echo "<li><a href='login/'>Login Page</a></li>";
echo "</ul>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Signup Debug</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin: 10px 0; }
        label { display: block; margin-bottom: 5px; }
        input { width: 300px; padding: 8px; }
        button { padding: 10px 20px; background: #007BFF; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <h2>Simple Signup Test Form</h2>
    
    <form method="POST" action="">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div class="form-group">
            <label for="confirmPassword">Confirm Password:</label>
            <input type="password" id="confirmPassword" name="confirmPassword" required>
        </div>
        
        <button type="submit">Register</button>
    </form>
</body>
</html> 