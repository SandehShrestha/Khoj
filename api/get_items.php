<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/auth.php';

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit();
}

try {
    $pdo = getDBConnection();
    
    $category = $_GET['category'] ?? 'all';
    $mine = isset($_GET['mine']) && $_GET['mine'] == '1';
    $userId = $_SESSION['user_id'] ?? null;
    
    if ($mine && $userId) {
        // Get only the logged-in user's items
        $stmt = $pdo->prepare("
            SELECT i.*, u.Username as found_by_name, u.Phone_Number as uploader_phone, u.Email as email 
            FROM Items i 
            LEFT JOIN User u ON i.Found_by_id = u.User_ID 
            WHERE i.Found_by_id = ?
            ORDER BY i.Created_at DESC
        ");
        $stmt->execute([$userId]);
    } else if ($category === 'all') {
        // Get all items
        $stmt = $pdo->prepare("
            SELECT i.*, u.Username as found_by_name, u.Phone_Number as uploader_phone, u.Email as email 
            FROM Items i 
            LEFT JOIN User u ON i.Found_by_id = u.User_ID 
            ORDER BY i.Created_at DESC
        ");
        $stmt->execute();
    } else {
        // Get items by category
        $stmt = $pdo->prepare("
            SELECT i.*, u.Username as found_by_name, u.Phone_Number as uploader_phone, u.Email as email 
            FROM Items i 
            LEFT JOIN User u ON i.Found_by_id = u.User_ID 
            WHERE i.item_category = ?
            ORDER BY i.Created_at DESC
        ");
        $stmt->execute([$category]);
    }
    
    $items = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'items' => $items]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?> 