<?php

$config_file = 'config.json';

if (file_exists($config_file)) {
    $config_data = file_get_contents($config_file);
    $config = json_decode($config_data, true); // true decodes as associative array

    if ($config === null && json_last_error() !== JSON_ERROR_NONE) {
        die("Error decoding JSON configuration: " . json_last_error_msg());
    }

    $start_year = $config['start_year'];
    $end_year = $config['end_year'];
    $issue_date = $config['issue_date'];
    $deadline = $config['deadline'];
    $student_access = $config['student_access'];
    $teacher_access = $config['teacher_access'];
} else {
    die("Configuration file '{$config_file}' not found.");
}

if ($teacher_access == 0) {
    // Unset all session variables
    $_SESSION = array();

    // If it's desired to kill the session, also delete the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Destroy the session
    session_destroy();
    header('Location: maintenance.php');
    exit;
}


session_start();

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$user = $_SESSION['user'];

// Extract SID from email
$email = $user['email'];
$sid = strtok($email, '@');

if (strlen($sid) > 4) {
    header("Location: redirect.php");
    exit;
}

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