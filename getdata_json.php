<?php
    require_once 'getdata.php';
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true, 
        'stuName' => $stuName ?? null,
        'stuClass' => $stuClass ?? null,
        'stuClassNum' => $stuClassNum ?? null,
        'stuID' => $stuID ?? null,
        'reflection' => $reflection ?? null,
        'activities' => $activities ?? [],
        'start_year' => $start_year ?? null,
        'end_year' => $end_year ?? null,
        'issue_date' => $issue_date ?? null
    ]);

