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

    session_start();

    if (!isset($_SESSION['user'])) {
        header('Location: index.php');
        exit;
    }

    $user = $_SESSION['user'];
    $email = $user['email'];
    $id = strtok($email, '@'); // Gets everything before '@'
    $identity = $user['identity'];

    if ($identity != 2 && $identity != 3) {
        header("Location: redirect.php");
        exit;
    }

    if ($teacher_access == 0) {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), 
                '', 
                time() - 42000,
                $params["path"], 
                $params["domain"],
                $params["secure"], 
                $params["httponly"]
            );
        }
        session_destroy();
        header('Location: maintenance.php');
        exit;
    }

    require_once 'dbconnect.php';
    try {
        $selectedClass = $_GET['class'] ?? 'all';
        $submissionStatus = $_GET['status'] ?? 'all';
        $sortOrder = $_GET['sort'] ?? '';

        if ($identity == 2) {
            $sql = "SELECT stu.class, stu.class_num, stu.name, sub.file_path, sub.submitted_at
                    FROM student stu
                    LEFT JOIN submission sub ON stu.sid = sub.sid AND sub.is_active = 1
                    INNER JOIN teacher t ON stu.class = t.class
                    WHERE t.id = :teacher_id";
            $params = [':teacher_id' => $id];
        } elseif ($identity == 3) {
            $sql = "SELECT stu.class, stu.class_num, stu.name, sub.file_path, sub.submitted_at
                    FROM student stu
                    LEFT JOIN submission sub ON stu.sid = sub.sid AND sub.is_active = 1";
            $params = [];
            if ($selectedClass !== 'all') {
                $sql .= " WHERE stu.class = :class";
                $params[':class'] = $selectedClass;
            }
        }

        // Add submission status filter
        if ($submissionStatus !== 'all') {
            $clause = ($identity == 3 && $selectedClass === 'all') ? ' WHERE' : ' AND';
            
            if ($submissionStatus === 'submitted') {
                $sql .= $clause . " sub.sid IS NOT NULL";
            } elseif ($submissionStatus === 'not_submitted') {
                $sql .= $clause . " sub.sid IS NULL";
            }
        }

        // Add sorting
        $orderBy = " ORDER BY stu.class ASC, stu.class_num ASC"; // Default
        if ($sortOrder === 'time_desc') {
            $orderBy = " ORDER BY IF(sub.submitted_at IS NULL, 1, 0), sub.submitted_at DESC, stu.class ASC, stu.class_num ASC";
        }
        $sql .= $orderBy;

        // Execute query
        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $records = [];
    }