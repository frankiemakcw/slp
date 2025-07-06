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

    if ($student_access == 0 && $teacher_access == 0) {
        header('Location: maintenance.php');
        exit;
    }
    session_start();

    if (isset($_SESSION['user'])) {
        header('Location: redirect.php');
        exit;
    }