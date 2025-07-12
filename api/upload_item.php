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

try {
    $pdo = getDBConnection();
    
    // Validate form data
    $itemName = trim($_POST['itemName'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $dateFound = $_POST['dateFound'] ?? '';
    $category = $_POST['category'] ?? '';
    $foundById = $_SESSION['user_id'] ?? null;
    if (!$foundById) {
        echo json_encode(['success' => false, 'message' => 'User not authenticated']);
        exit();
    }
    
    if (empty($itemName) || empty($description) || empty($location) || empty($dateFound) || empty($category)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit();
    }
    
    // Handle image upload
    $imageUrl = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        
        // Create uploads directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileInfo = pathinfo($_FILES['image']['name']);
        $extension = strtolower($fileInfo['extension']);
        
        // Validate file type
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($extension, $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and GIF are allowed']);
            exit();
        }
        
        // Generate unique filename
        $filename = uniqid() . '.' . $extension;
        $filepath = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
            $imageUrl = 'uploads/' . $filename;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
            exit();
        }
    }
    
    // Insert item into database
    $stmt = $pdo->prepare("INSERT INTO Items (Item_Name, Description, Location, Date_found, Image_url, Found_by_id, item_category) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$itemName, $description, $location, $dateFound, $imageUrl, $foundById, $category]);
    $itemId = $pdo->lastInsertId();
    
    // Fetch the inserted item with user info
    $stmt = $pdo->prepare("SELECT Items.*, User.Username FROM Items JOIN User ON Items.Found_by_id = User.User_ID WHERE Items.Item_ID = ?");
    $stmt->execute([$itemId]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($item) {
        echo json_encode(['success' => true, 'message' => 'Item uploaded successfully', 'item' => $item]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to fetch uploaded item']);
    }
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?> 