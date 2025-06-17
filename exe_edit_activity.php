<?php
require_once 'getdata.php';
header('Content-Type: application/json');

// Check if all required fields are filled and trim all string inputs
$activityName = isset($_POST['activity_name']) ? trim($_POST['activity_name']) : '';
$organizer = isset($_POST['organizer']) ? trim($_POST['organizer']) : '';
$role = isset($_POST['role']) ? trim($_POST['role']) : '';

// Check required fields
if (!isset($_POST['id']) || empty($activityName) || empty($organizer) || empty($role)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// Get dates based on type
if ($_POST['date_type'] === 'single') {
    $startDate = $endDate = $_POST['activity_date'];
} else {
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    
    // Validate date range
    if (strtotime($endDate) <= strtotime($startDate)) {
        echo json_encode(['success' => false, 'message' => 'End date must be after start date']);
        exit;
    }
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

    // Update the activity
    $stmt = $pdo->prepare("UPDATE activity SET 
                          start_date = :start_date,
                          end_date = :end_date,
                          activity_name = :activity_name,
                          organizer = :organizer,
                          role = :role
                          WHERE id = :id");
    
    $stmt->bindParam(':id', $_POST['id']);
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date', $endDate);
    $stmt->bindParam(':activity_name', $activityName);
    $stmt->bindParam(':organizer', $organizer);
    $stmt->bindParam(':role', $role);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}