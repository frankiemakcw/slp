<?php
    require_once 'getdata.php';
    if ($is_submitted) {
        header("Location: submitted.php");
        exit;
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Learning Profile</title>
    <link rel="stylesheet" href="style/styles.css">
</head>
<body>

    <h2>Personal Reflections</h2>
   
    <div class="container">
        <textarea id="reflectionText" placeholder="Type your reflection here..." spellcheck="true"><?php 
            if (isset($reflectionData['reflection'])) {
                echo htmlspecialchars(trim($reflectionData['reflection']), ENT_QUOTES, 'UTF-8');
            }
        ?></textarea>
        <div class="form-actions edit-btn">
            <button type="button" class="btn-red" id="btn-quit" onclick="window.location.href='main.php'">Back</button>
            <button type="button" class="btn-green" id="btn-save">Save Reflection</button>
        </div>
    </div>

    <script src="script/reflection.js"></script>
</body>
</html>