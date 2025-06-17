<?php
    require_once 'getdata.php';
    if ((substr($stuClass, 0, 1) === '1')) {
        $pdfPath = 'calendar/calendar_s1.pdf';
    } else {
        $pdfPath = 'calendar/calendar_all.pdf';
    }
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
</head>
<body>

    <h2>Ying Wa Girls' School</h2>
    <h2>Student Learning Profile (SLP)</h2>
    <h2>20<?= htmlspecialchars($start_year) ?>-20<?= htmlspecialchars($end_year) ?></h2>
   
    <div class="container container-stuinfo">
        <div class="student-info">
            <div class="info-item">
                <span class="label">NAME:</span>
                <span class="value"><?= htmlspecialchars($stuName) ?></span>
            </div>
            <div class="info-item">
                <span class="label">CLASS:</span>
                <span class="value"><?= htmlspecialchars($stuClass) ?> (<?= htmlspecialchars($stuClassNum) ?>)</span>
            </div>
            <div class="info-item">
                <span class="label">STUDENT ID:</span>
                <span class="value"><?= htmlspecialchars($stuID) ?></span>
            </div>
        </div>

        <div class="action-buttons container-rowwrap">
            <button onclick="window.location.href='instructions.php'" class="btn-blue">Instructions</button>
            <a href="<?php echo $pdfPath; ?>"  target="_blank">
                <button class="btn-blue">OLE Calendar</button>
            </a>
            <button onclick="window.location.href='logout.php'" class="btn-red">Logout</button>
        </div>

    </div>
    
    <div class="container">
    <h2>Activities</h2>

        <?php if (!empty($activities)): ?>
            <div class="activities-table">
                <?php foreach ($activities as $index => $activity): ?>
                    <div class="activity-card">
                        <div class="activity-row">
                            <div class="activity-number"><?= $index + 1 ?>.</div>
                            <div class="activity-date">
                                <?php 
                                $startDate = new DateTime($activity['start_date']);
                                $endDate = new DateTime($activity['end_date']);
                                
                                if ($activity['start_date'] == $activity['end_date']) {
                                    echo $startDate->format('Y/m/d');
                                } else {
                                    echo $startDate->format('Y/m/d').' - '.$endDate->format('Y/m/d');
                                }
                                ?>
                            </div>
                            <div class="activity-name"><?= htmlspecialchars($activity['activity_name']) ?></div>
                            <div class="activity-organizer"><?= htmlspecialchars($activity['organizer']) ?></div>
                            <div class="activity-role"><?= htmlspecialchars($activity['role']) ?></div>
                        </div>
                        <div class="activity-actions edit-btn">
                            <button onclick="window.location.href='edit_activity.php?id=<?= $activity['id'] ?>'" class="small-btn btn-blue">Edit</button>
                            <button onclick="deleteActivity(<?= $activity['id'] ?>)" class="small-btn btn-red">Delete</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p id="no-records-message">No activities saved yet.</p>
        <?php endif; ?>
  
        <div class="form-actions edit-btn">
            <button type="button" onclick="window.location.href='new_activity.php'" class="btn-green">New Activity</button>
        </div>
    </div>

    <?php if (substr($stuClass, 0, 1) === '3' || substr($stuClass, 0, 1) === '6') : ?>
        <div class="container">
            <h2>Personal Reflections</h2>
            <div>
                <?php if (!empty($reflection)): ?>
                    <pre id="reflection"><?= htmlspecialchars($reflection) ?></pre>
                <?php else: ?>
                    <p id="no-records-message">No reflection yet.</p>
                <?php endif; ?>
            </div>
            <div class="form-actions edit-btn">
                <button type="button" onclick="window.location.href='reflection.php'" class="btn-green">Edit Reflection</button>
            </div>
        </div>
    <?php endif; ?>

    <div class="container edit-btn" id="not-submitted">
        <div class="form-actions">
            <button type="button" class="btn-blue" id="previewpdf" onclick="previewPDF()">Preview PDF</button>
            <button type="button" class="btn-purple" id="submitpdf" onclick="submitPDF()">Submit</button>
        </div>
    </div>

    <script src="script/main.js"></script>
    <script src="script/notosanstcedit-normal.js"></script>
</body>
</html>