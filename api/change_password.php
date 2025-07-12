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
$currentPassword = $_POST['current_password'] ?? '';
$newPassword = $_POST['new_password'] ?? '';

// Debug: log received data
if (!$currentPassword || !$newPassword) {
    echo json_encode([
        'success' => false,
        'message' => 'All fields are required.',
        'debug' => [
            'userId' => $userId,
            'post_data' => $_POST
        ]
    ]);
    exit();
}

try {
    $pdo = getDBConnection();
    // Get current password hash
    $stmt = $pdo->prepare('SELECT Password_hash FROM User WHERE User_ID = ?');
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user || !password_verify($currentPassword, $user['Password_hash'])) {
        echo json_encode(['success' => false, 'message' => 'Current password is incorrect.']);
        exit();
    }
    // Update password
    $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('UPDATE User SET Password_hash = ? WHERE User_ID = ?');
    if (!$stmt->execute([$newHash, $userId])) {
        $errorInfo = $stmt->errorInfo();
        echo json_encode(['success' => false, 'message' => 'Failed to update password.', 'sql_error' => $errorInfo]);
        exit();
    }
    echo json_encode(['success' => true, 'message' => 'Password updated successfully.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} 