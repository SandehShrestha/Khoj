<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/auth.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$userId = $_SESSION['user_id'] ?? null;
$username = trim($_POST['username'] ?? '');
$phone = trim($_POST['phone'] ?? '');

// Debug: echo received data
if (!$username || !$phone) {
    echo json_encode([
        'success' => false,
        'message' => 'Username and phone number are required.',
        'debug' => [
            'received_username' => $username,
            'received_phone' => $phone,
            'post_data' => $_POST
        ]
    ]);
    exit();
}

try {
    $pdo = getDBConnection();
    // Check if username is taken by another user
    $stmt = $pdo->prepare('SELECT User_ID FROM User WHERE Username = ? AND User_ID != ?');
    $stmt->execute([$username, $userId]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Username is already taken.']);
        exit();
    }
    // Check if phone is taken by another user
    $stmt = $pdo->prepare('SELECT User_ID FROM User WHERE Phone_Number = ? AND User_ID != ?');
    $stmt->execute([$phone, $userId]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Phone number is already taken.']);
        exit();
    }
    // Update user info
    $stmt = $pdo->prepare('UPDATE User SET Username = ?, Phone_Number = ? WHERE User_ID = ?');
    if (!$stmt->execute([$username, $phone, $userId])) {
        $errorInfo = $stmt->errorInfo();
        echo json_encode(['success' => false, 'message' => 'Failed to update user info.', 'sql_error' => $errorInfo]);
        exit();
    }
    // Get updated info
    $stmt = $pdo->prepare('SELECT Username, Email, Phone_Number FROM User WHERE User_ID = ?');
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    // Update session username if needed
    $_SESSION['username'] = $user['Username'];
    echo json_encode(['success' => true, 'user' => $user]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} 