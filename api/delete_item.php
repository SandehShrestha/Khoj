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

$itemId = $_POST['item_id'] ?? null;
$userId = $_SESSION['user_id'] ?? null;

if (!$itemId || !$userId) {
    echo json_encode(['success' => false, 'message' => 'Missing item ID or user not authenticated']);
    exit();
}

try {
    $pdo = getDBConnection();
    // Check if the item belongs to the user and is not claimed
    $stmt = $pdo->prepare('SELECT * FROM Items WHERE Item_ID = ? AND Found_by_id = ? AND Is_claimed = 0');
    $stmt->execute([$itemId, $userId]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$item) {
        echo json_encode(['success' => false, 'message' => 'Item not found, not yours, or already claimed']);
        exit();
    }
    // Delete the item
    $stmt = $pdo->prepare('DELETE FROM Items WHERE Item_ID = ?');
    $stmt->execute([$itemId]);
    echo json_encode(['success' => true, 'message' => 'Item deleted successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} 