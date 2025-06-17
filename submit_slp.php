<?php
require_once 'getdata.php';

header('Content-Type: application/json');

try {
    // Check if PDF file was uploaded
    if (!isset($_FILES['pdf'])) {
        throw new Exception('No PDF file uploaded');
    }
    
    // Validate student exists (you might want to add more validation)
    $stmt = $pdo->prepare("SELECT sid FROM student WHERE sid = ?");
    $stmt->execute([$sid]);
    if (!$stmt->fetch()) {
        throw new Exception('Student not found');
    }
    
    // Create uploads directory if it doesn't exist
    if (!file_exists('uploads')) {
        mkdir('uploads', 0755, true);
    }
    
    // Generate unique filename
    $filename = 'SLP_' . $start_year . $end_year . '_' . $stuClass . sprintf("%02d", $stuClassNum) . '_' . (new DateTime('now +8 hours'))->format('YmdHis') . '.pdf';
    $filepath = 'uploads/' . $filename;
    
    // Move uploaded file
    if (!move_uploaded_file($_FILES['pdf']['tmp_name'], $filepath)) {
        throw new Exception('Failed to save PDF file');
    }

    // Record submission in database
    $stmt = $pdo->prepare("
        INSERT INTO submission 
        (sid, file_path, submitted_at, is_active) 
        VALUES (?, ?, NOW(), 1)
    ");
    $stmt->execute([$sid, $filepath]);
    
    echo json_encode(['success' => true, 'message' => 'SLP submitted successfully']);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}