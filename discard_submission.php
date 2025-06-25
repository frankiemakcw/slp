<?php
    require_once 'getdata.php';
    header('Content-Type: text/plain');

    try {
        $update = $pdo->prepare("UPDATE submission SET is_active = 0 WHERE sid = ?");
        $update->execute([$sid]);
        
        echo json_encode(["success" => true, "message" => "Submission discarded"]);
        
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    }
