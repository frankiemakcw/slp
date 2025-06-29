<?php
    require_once 'getdata.php';
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
    <div class="container">
        <h2>Student Learning Profile (SLP) Instructions</h2>
        <h3>Purposes</h3>
        <ul>
            <li>To serve as evidence of your holistic development during secondary school</li>
            <li>To demonstrate your personal qualities in applications for tertiary education</li>
        </ul>

        <h3>Part I: Activities</h3>
        <ul>
            <li>Record the activities (such as courses, programs, competitions, talks, exhibitions, etc.) you participated in, both inside and outside of school, during the following periods:
                <ul>
                    <li>S1: <strong>Sep 20<?= htmlspecialchars($start_year) ?> to Apr 20<?= htmlspecialchars($end_year) ?></strong></li>
                    <li>S2-S5: <strong>May 20<?= htmlspecialchars($start_year) ?> to Apr 20<?= htmlspecialchars($end_year) ?></strong></li>
                </ul>
            </li>
            <li><strong>DO NOT</strong> include those information that will be reported by your teachers, including
                <ul>
                    <li>Memberships (e.g., art club member, basketball team member)</li>
                    <li>Posts (e.g., class committee chairlady)</li>
                    <li>Awards that will be reported by teachers</li>
                    <li>S.1 High Event</li>
                    <li>S.2 Volunteer Training Programme</li>
                    <li>S.4 Learning Project</li>
                </ul>
            </li>
            <li>Check spelling. Use full names and capitalize the first letter of activity and organization names.</li>
            <li>For school events, refer to the OLE calendar and write "Ying Wa Girls' School" as the organizer without specifying any departments or teachers.</li>
            <li>You are advised to fill in at least 10 items. You may include more than 10 if you wish but ensure the items fit properly without overlapping other text. Only select meaningful activities, rather than recording all activities.<strong>Quality is more important than quantity.</strong></li>
            <li>若活動只有中文名稱，請使用中文紀錄活動籌辦者及角色。</li>
        </ul>
     
        <h3>Part II: Personal Reflections</h3>
        <ul>
            <li><strong>Required only for S3 & S6.</strong></li>
            <li>You may write in either Chinese or English, up to approximately 500 words.</li>
            <li>Preview the PDF file to ensure that the reflections fit properly and do not overlap with other text.</li>
        </ul>

        <h3>Submission</h3>
        <ul>
            <li><strong class="deadline">Deadline: <?= htmlspecialchars($deadline) ?></strong></li>
            <li>Late submission or reprint incurs a <strong>$40 fee</strong>.</li>
        </ul>
        
        <h4 style="text-align: center;">If you have any questions, please contact Mr. Mak (FM) at fm@ywgs.edu.hk.</h4>

        <div class="form-actions">
            <button type="button" class="btn-blue" onClick="window.location.href='main.php'">Start</button>
        </div>
    </div>
</body>
</html>
   
