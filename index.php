<?php
    require_once 'getdata_login.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Learning Profile</title>
    <link rel="stylesheet" href="style/styles.css">
    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body>

    

    <div class="container">
        <h2>Ying Wa Girls' School</h2>
        <h2>Student Learning Profile (SLP)</h2>
        <h2>20<?= htmlspecialchars($start_year) ?>-20<?= htmlspecialchars($end_year) ?></h2>
        <br>
        <h2>Login with your school Gmail account.</h2>      
        <div class="login-section">
            <div id="g_id_onload" 
                data-client_id="1025312216370-1ba1liogt0rbgunrf7985rqutesratsv.apps.googleusercontent.com"
                data-context="signin" 
                data-ux_mode="popup" 
                data-callback="handleCredentialResponse" 
                data-auto_select="true"
                data-itp_support="true">
            </div>

            <div class="g_id_signin" 
                data-type="standard" 
                data-shape="rectangular" 
                data-theme="outline" 
                data-text="signin_with"
                data-size="large" 
                data-logo_alignment="left">
            </div>
        </div>
        <br>
        <h4 style="text-align: center;">If you have any questions, please contact Mr. Mak (FM) at fm@ywgs.edu.hk.</h4>
    </div>
    <script src="script/login.js"></script>
</body>
</html>