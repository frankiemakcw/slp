<?php

require_once 'getdata_teacher.php';

try {   
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