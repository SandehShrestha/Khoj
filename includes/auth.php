<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// User registration function
function registerUser($username, $email, $password, $phone) {
    try {
        $pdo = getDBConnection();
        
        // Check if username or email already exists
        $stmt = $pdo->prepare("SELECT User_ID FROM User WHERE Username = ? OR Email = ? OR Phone_Number = ?");
        $stmt->execute([$username, $email, $phone]);
        
        if ($stmt->rowCount() > 0) {
            return ['success' => false, 'message' => 'Username, email, or phone number already exists'];
        }
        
        // Hash password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user
        $stmt = $pdo->prepare("INSERT INTO User (Username, Email, Password_hash, Phone_Number) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $email, $password_hash, $phone]);
        
        return ['success' => true, 'message' => 'Registration successful'];
        
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
    }
}

// User login function
function loginUser($username_or_email, $password) {
    try {
        $pdo = getDBConnection();
        
        // Check if input is email or username
        $isEmail = filter_var($username_or_email, FILTER_VALIDATE_EMAIL);
        
        if ($isEmail) {
            $stmt = $pdo->prepare("SELECT * FROM User WHERE Email = ?");
        } else {
            $stmt = $pdo->prepare("SELECT * FROM User WHERE Username = ?");
        }
        
        $stmt->execute([$username_or_email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['Password_hash'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['User_ID'];
            $_SESSION['username'] = $user['Username'];
            $_SESSION['email'] = $user['Email'];
            $_SESSION['logged_in'] = true;
            
            return ['success' => true, 'message' => 'Login successful', 'user' => $user];
        } else {
            return ['success' => false, 'message' => 'Invalid username/email or password'];
        }
        
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Login failed: ' . $e->getMessage()];
    }
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// Logout function
function logout() {
    session_unset();
    session_destroy();
    return ['success' => true, 'message' => 'Logged out successfully'];
}

// Get current user data
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT User_ID, Username, Email, Phone_Number, Created_At FROM User WHERE User_ID = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}
?> 