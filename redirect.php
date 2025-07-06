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

    // Extract SID from email
    $email = $user['email'];
    $identity = $user['identity'];

    if ($identity == 2 || $identity == 3) {
        if ($teacher_access == 1) {
            header("Location: teacher.php");
            exit;
        } else {
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
            header("Location: maintenance.php");
            exit;
        }
    } else if ($identity == 1) {
        if ($student_access == 1) {
            header('Location: instructions.php');
            exit;
        } else {
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
    } else {
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
        header('Location: access_denied.php');
        exit;
    }

    