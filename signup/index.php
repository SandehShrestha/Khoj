<?php
require_once __DIR__ . '/../includes/auth.php';

$message = '';
$messageType = '';
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    
    // Validation
    if (empty($username)) {
        $errors['username'] = 'Username is required';
    }
    
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }
    
    if (empty($phone)) {
        $errors['phone'] = 'Phone number is required';
    }
    
    if (empty($password)) {
        $errors['password'] = 'Password is required';
    } elseif (strlen($password) < 6) {
        $errors['password'] = 'Password must be at least 6 characters';
    }
    
    if ($password !== $confirmPassword) {
        $errors['confirmPassword'] = 'Passwords do not match';
    }
    
    if (empty($errors)) {
        // Register user
        $result = registerUser($username, $email, $password, $phone);
        
        if ($result['success']) {
            $message = $result['message'];
            $messageType = 'success';
            // Redirect to login page after successful registration
            header("Refresh: 2; URL=../login/index.php");
        } else {
            $message = $result['message'];
            $messageType = 'error';
        }
    } else {
        $message = 'Please fix the errors below.';
        $messageType = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up - ItemHunt</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="signup-container">
        <h2>Create Account</h2>
        
        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <form id="signupForm" method="POST" action="" novalidate>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                <div class="error" id="usernameError"><?php echo htmlspecialchars($errors['username'] ?? ''); ?></div>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                <div class="error" id="emailError"><?php echo htmlspecialchars($errors['email'] ?? ''); ?></div>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <div class="phone-input-group">
                    <span class="phone-prefix">+977</span>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" required maxlength="10" pattern="[0-9]{10}" placeholder="98XXXXXXXX">
                </div>
                <div class="error" id="phoneError"><?php echo htmlspecialchars($errors['phone'] ?? ''); ?></div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required minlength="6">
                <button type="button" class="toggle-password-btn" tabindex="-1" aria-label="Show/Hide Password" data-target="password">
                    <img src="../images/eyeclosed.png" alt="Toggle Password">
                </button>
                <div class="error" id="passwordError"><?php echo htmlspecialchars($errors['password'] ?? ''); ?></div>
            </div>
            
            <div class="form-group">
                <label for="confirmPassword">Confirm Password</label>
                <input type="password" id="confirmPassword" name="confirmPassword" required>
                <button type="button" class="toggle-password-btn" tabindex="-1" aria-label="Show/Hide Confirm Password" data-target="confirmPassword">
                    <img src="../images/eyeclosed.png" alt="Toggle Password">
                </button>
                <div class="error" id="confirmPasswordError"><?php echo htmlspecialchars($errors['confirmPassword'] ?? ''); ?></div>
            </div>
            
            <button type="submit" class="signup-btn">Sign Up</button>
        </form>
        
        <div class="login-link">
            Already have an account? <a href="../login/index.php">Log in</a>
        </div>
    </div>
    
    <script src="script.js"></script>
</body>
</html> 