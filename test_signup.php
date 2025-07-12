<?php
require_once __DIR__ . '/includes/auth.php';

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    
    echo "<h3>Form Data Received:</h3>";
    echo "<ul>";
    echo "<li>Username: " . htmlspecialchars($username) . "</li>";
    echo "<li>Email: " . htmlspecialchars($email) . "</li>";
    echo "<li>Phone: " . htmlspecialchars($phone) . "</li>";
    echo "<li>Password: " . str_repeat('*', strlen($password)) . "</li>";
    echo "</ul>";
    
    if (!empty($username) && !empty($email) && !empty($phone) && !empty($password)) {
        // Register user
        $result = registerUser($username, $email, $password, $phone);
        
        echo "<h3>Registration Result:</h3>";
        echo "<p>Success: " . ($result['success'] ? 'Yes' : 'No') . "</p>";
        echo "<p>Message: " . htmlspecialchars($result['message']) . "</p>";
        
        if ($result['success']) {
            $message = $result['message'];
            $messageType = 'success';
        } else {
            $message = $result['message'];
            $messageType = 'error';
        }
    } else {
        $message = 'All fields are required';
        $messageType = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Test Signup</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin: 10px 0; }
        label { display: block; margin-bottom: 5px; }
        input { width: 300px; padding: 8px; }
        button { padding: 10px 20px; background: #007BFF; color: white; border: none; cursor: pointer; }
        .message { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <h1>Test Signup Form</h1>
    
    <?php if ($message): ?>
        <div class="message <?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    
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
        
        <button type="submit">Register</button>
    </form>
    
    <h3>Test Links:</h3>
    <ul>
        <li><a href="simple_test.php">Simple Test</a></li>
        <li><a href="debug_login.php">Debug Login</a></li>
        <li><a href="signup/">Original Signup Page</a></li>
        <li><a href="login/">Login Page</a></li>
    </ul>
</body>
</html> 