<?php
require_once 'getdata.php'; // Your database connection file

// Get data from POST request
$reflection = $_POST['reflection'] ?? '';

if (empty($sid) || empty($reflection)) {
    http_response_code(400);
    die("Student ID and reflection text are required");
}

try {
    // First check if reflection exists for this student
    $checkQuery = "SELECT sid FROM reflection WHERE sid = ?";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->execute([$sid]);
    $exists = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($exists) {
        // Update existing reflection
        $updateQuery = "UPDATE reflection SET reflection = ? WHERE sid = ?";
        $stmt = $pdo->prepare($updateQuery);
        $stmt->execute([$reflection, $sid]);
    } else {
        // Insert new reflection
        $insertQuery = "INSERT INTO reflection (sid, reflection) VALUES (?, ?)";
        $stmt = $pdo->prepare($insertQuery);
        $stmt->execute([$sid, $reflection]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    die("Database error: " . $e->getMessage());
}