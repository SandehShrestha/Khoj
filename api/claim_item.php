<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/auth.php';

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit();
}

$itemId = $input['item_id'] ?? null;
$claimMessage = $input['claim_message'] ?? '';

if (!$itemId) {
    echo json_encode(['success' => false, 'message' => 'Item ID is required']);
    exit();
}

try {
    $pdo = getDBConnection();
    
    // Check if item exists and is not already claimed
    $stmt = $pdo->prepare("SELECT * FROM Items WHERE Item_ID = ? AND Is_claimed = FALSE");
    $stmt->execute([$itemId]);
    $item = $stmt->fetch();
    
    if (!$item) {
        echo json_encode(['success' => false, 'message' => 'Item not found or already claimed']);
        exit();
    }
    
    // Check if user has already claimed this item
    $stmt = $pdo->prepare("SELECT * FROM Claims WHERE Item_ID = ? AND Claimed_by_id = ?");
    $stmt->execute([$itemId, $_SESSION['user_id']]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'You have already claimed this item']);
        exit();
    }
    
    // Insert claim
    $stmt = $pdo->prepare("INSERT INTO Claims (Item_ID, Claimed_by_id, Claim_message) VALUES (?, ?, ?)");
    $stmt->execute([$itemId, $_SESSION['user_id'], $claimMessage]);
    
    echo json_encode(['success' => true, 'message' => 'Claim submitted successfully']);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?> 