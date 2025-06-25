<?php
    require_once 'getdata.php';

    header('Content-Type: application/json');

    // Check if all required fields are filled and trim all string inputs
    $activityName = isset($_POST['activity_name']) ? trim($_POST['activity_name']) : '';
    $organizer = isset($_POST['organizer']) ? trim($_POST['organizer']) : '';
    $role = isset($_POST['role']) ? trim($_POST['role']) : '';

    if (empty($activityName) || empty($organizer) || empty($role)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }

    // Handle dates based on date type
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

    // Insert into database
    try {
        $stmt = $pdo->prepare("INSERT INTO activity (sid, start_date, end_date, activity_name, organizer, role) 
                            VALUES (:sid, :start_date, :end_date, :activity_name, :organizer, :role)");
        
        $stmt->bindParam(':sid', $sid);
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