<?php
    session_start();

    if (!isset($_SESSION['user'])) {
        header('Location: index.php');
        exit;
    }

    $user = $_SESSION['user'];

    // Extract SID from email
    $email = $user['email'];
    $sid = strtok($email, '@'); // Gets everything before '@'

    if (strlen($sid) <= 3) {
        header("Location: teacher.php");
        exit;
    }

    // Include database connection
    require_once 'dbconnect.php';

    // Query the student table for name
    $stmt = $pdo->prepare("SELECT `name`, `class`, `class_num` FROM `student` WHERE sid = ?");
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
    $stuID = strtoupper($sid);
    $reflection = $reflectionData['reflection'] ?? "";
    $is_submitted = !empty($submission);
    $submissionTime = $submission['submitted_at'] ?? null;
    $filepath = $submission['file_path'] ?? null;

    