<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$user = $_SESSION['user'];

// Extract SID from email (same validation as teacher_getdata.php)
$email = $user['email'];
$sid = strtok($email, '@');

require_once 'dbconnect.php';

try {
    // Get filters from URL
    $selectedClass = $_GET['class'] ?? 'all';
    $submissionStatus = $_GET['status'] ?? 'all';
    
    // Build the query (same as teacher_getdata.php)
    $sql = "SELECT stu.class, stu.class_num, stu.name, sub.file_path, sub.submitted_at
            FROM student stu
            LEFT JOIN submission sub ON stu.sid = sub.sid AND sub.is_active = 1";
    
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
    
    $sql .= " ORDER BY stu.class ASC, stu.class_num ASC";
    
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Check if there are any files to download
    $hasFiles = false;
    foreach ($records as $row) {
        if (!empty($row['file_path'])) {
            $hasFiles = true;
            break;
        }
    }
    
    if (!$hasFiles) {
        die("No files available for download with the current filters.");
    }
    
    // Create zip file
    $zip = new ZipArchive();
    $zipFilename = tempnam(sys_get_temp_dir(), 'slp_zip_');
    
    if ($zip->open($zipFilename, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
        die("Cannot create zip file");
    }
    
    // Add files to zip
    foreach ($records as $row) {
        if (!empty($row['file_path'])) {
            $filePath = $row['file_path'];
            if (file_exists($filePath) && is_readable($filePath)) {
                // Get original filename (without path)
                $originalFilename = basename($filePath);
                
                // Take first 13 characters of original filename
                $shortFilename = substr($originalFilename, 0, 13);
                
                // Get file extension
                $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                
                // Create final filename (13chars + extension)
                $zipName = $shortFilename . '.' . $extension;
                
                $zip->addFile($filePath, $zipName);
            }
        }
    }
    
    $zip->close();
    
    // Send the zip to browser
    header('Content-Type: application/zip');
    $adjustedTime = time() + (8 * 3600); // 8 hours * 3600 seconds/hour
    header('Content-Disposition: attachment; filename="slp_submissions_' . date('Ymd_Hi', $adjustedTime) . '.zip"');
    header('Content-Length: ' . filesize($zipFilename));
    header('Pragma: no-cache');
    header('Expires: 0');
    
    readfile($zipFilename);
    unlink($zipFilename); // Delete temp file
    exit;
    
} catch (PDOException $e) {
    error_log("Database error in download_all.php: " . $e->getMessage());
    die("An error occurred while preparing the download. Please try again later.");
} catch (Exception $e) {
    error_log("Error in download_all.php: " . $e->getMessage());
    die("An error occurred while creating the zip file.");
}