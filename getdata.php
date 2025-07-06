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

    if ($student_access == 0) {
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
    $sid = strtok($email, '@'); // Gets everything before '@'
    $identity = $user['identity'];

    if ($identity != 1) {
        header("Location: redirect.php");
        exit;
    }

    // Include database connection
    require_once 'dbconnect.php';

    

    // Query the student table for name
    $stmt = $pdo->prepare("SELECT `name`, `class`, `class_num`, `preview_id` FROM `student` WHERE sid = ?");
    $stmt->execute([$sid]);
    $studentData = $stmt->fetch(PDO::FETCH_ASSOC);

    // Query the reflection table for reflection
    $stmt = $pdo->prepare("SELECT reflection FROM reflection WHERE sid = ?");
    $stmt->execute([$sid]);
    $reflectionData = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch activities for this student
    $activities = [];
    $stmt = $pdo->prepare("SELECT id, start_date, end_date, activity_name, organizer, role FROM activity WHERE sid = ? ORDER BY start_date, end_date");
    $stmt->execute([$sid]);
    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if submitted
    $stmt = $pdo->prepare("SELECT * FROM submission WHERE sid = ? AND is_active = 1");
    $stmt->execute([$sid]);
    $submission = $stmt->fetch(PDO::FETCH_ASSOC);

    $stuName = strtoupper($studentData['name']);
    $stuClass = strtoupper($studentData['class']);
    $stuClassNum = $studentData['class_num'];
    $previewID = $studentData['preview_id'];
    $stuID = strtoupper($sid);
    $reflection = $reflectionData['reflection'] ?? "";
    $is_submitted = !empty($submission);
    $submissionTime = $submission['submitted_at'] ?? null;
    $filepath = $submission['file_path'] ?? null;

    