<?php
require_once 'getdata.php';
header('Content-Type: application/json');

// Check if ID is provided
if (!isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

try {
    // First verify the activity belongs to the current student
    $checkStmt = $pdo->prepare("SELECT id FROM activity WHERE id = :id AND sid = :sid");
    $checkStmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
    $checkStmt->bindParam(':sid', $sid, PDO::PARAM_STR); 
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'Activity not found or access denied']);
        exit;
    }

    // If verification passed, proceed with deletion
    $deleteStmt = $pdo->prepare("DELETE FROM activity WHERE id = :id");
    $deleteStmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
    
    if ($deleteStmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}