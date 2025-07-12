<?php
require_once __DIR__ . '/../includes/auth.php';

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_or_email = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validation
    if (empty($username_or_email) || empty($password)) {
        $message = 'Please enter both username/email and password';
        $messageType = 'error';
    } else {
        // Attempt login
        $result = loginUser($username_or_email, $password);
        
        if ($result['success']) {
            $message = $result['message'];
            $messageType = 'success';
            // Redirect to main application after successful login
            header("Refresh: 1; URL=../index.php");
        } else {
            $message = $result['message'];
            $messageType = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ItemHunt</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-box">
        <div class="login-container">
            <h2>Welcome!</h2>
            <p id="LTD">Login To Proceed</p>
            
            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="input-group">
                    <input type="text" id="username" name="username" placeholder="Username/Email:" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                </div>
                <div class="input-group">
                    <input type="password" id="password" name="password" placeholder="Password:" required>
                    <button type="button" class="toggle-password">
                        <img src="../images/eyeclosed.png" alt="Toggle Password Visibility" class="eye-icon">
                    </button>
                </div>
                <button type="submit" class="login-button">Log in</button>
                <div class="link-row">
                    <p class="sign-up">
                        <a href="../signup/index.php">Create account</a>
                    </p>
                    <p class="forgot-pass"><a href="#">Forgot password</a></p>
                </div>
            </form>
        </div>
    </div>
    
    <script src="script.js"></script>
</body>
</html> 