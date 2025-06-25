<?php
    require_once 'getdata.php';

    header('Content-Type: application/json');


    // Check if PDF file was uploaded
    if (!isset($_FILES['pdf'])) {
        throw new Exception('No PDF file uploaded');
    }


    // Create uploads directory if it doesn't exist
    if (!file_exists('preview')) {
        mkdir('preview', 0755, true);
    }

    // Generate unique filename
    $filename = 'SLP_preview_' . $start_year . $end_year . '_' . $stuClass . sprintf("%02d", $stuClassNum) . '_' . $previewID . '.pdf';
    $filepath = 'preview/' . $filename;

    // Move uploaded file
    if (!move_uploaded_file($_FILES['pdf']['tmp_name'], $filepath)) {
        throw new Exception('Failed to save PDF file');
    }

    echo json_encode(['success' => true, 'message' => 'Preview saved successfully']);
    
