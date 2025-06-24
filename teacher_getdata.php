<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$user = $_SESSION['user'];

// Extract SID from email
$email = $user['email'];
$sid = strtok($email, '@');

require_once 'dbconnect.php';
try {
    // Get filters from user
    $selectedClass = $_GET['class'] ?? 'all';
    $submissionStatus = $_GET['status'] ?? 'all';
    $sortOrder = $_GET['sort'] ?? '';
    
    // Build the query
    $sql = "SELECT stu.class, stu.class_num, stu.name, sub.file_path, sub.submitted_at
            FROM student stu
            LEFT JOIN submission sub ON stu.sid = sub.sid AND sub.is_active = 1";
    
    // Add filters
    $where = [];
    $params = [];
    
    if ($selectedClass !== 'all') {
        $where[] = "stu.class = :class";
        $params[':class'] = $selectedClass;
    }
    
    if ($submissionStatus !== 'all') {
        if ($submissionStatus === 'submitted') {
            $where[] = "sub.sid IS NOT NULL";
        } elseif ($submissionStatus === 'not_submitted') {
            $where[] = "sub.sid IS NULL";
        }
    }
    
    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    
    // Add sorting
    $orderBy = " ORDER BY stu.class ASC, stu.class_num ASC"; // Default sorting

    if ($sortOrder === 'time_desc') {
        $orderBy = " ORDER BY sub.submitted_at DESC"; // Newest first
    }

    $sql .= $orderBy;
    
    $stmt = $pdo->prepare($sql);
    
    // Bind parameters
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    
    $stmt->execute();
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $records = [];
}