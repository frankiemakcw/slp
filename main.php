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

    <div class="container">
        <div>
            <h2>Ying Wa Girls' School</h2>
            <h2>Student Learning Profile (SLP)</h2>
            <h2>20<?= htmlspecialchars($start_year) ?>-20<?= htmlspecialchars($end_year) ?></h2>
        </div>
        <div class="container-space-between">
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

            <div class="action-buttons">
                <button onclick="window.location.href='instructions.php'" class="btn-blue">Instructions</button>
                <a href="<?php echo $pdfPath; ?>"  target="_blank">
                    <button class="btn-blue">OLE Calendar</button>
                </a>
                <button onclick="window.location.href='logout.php'" class="btn-red">Logout</button>
            </div>
        </div>

    </div>
    
    <div class="container">
    <h2>Activities</h2>

        <?php if (!empty($activities)): ?>
            <table class="activity-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Date</th>
                        <th>Activity</th>
                        <th>Organizer</th>
                        <th>Role</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $counter = 1;
                    foreach ($activities as $activity): 
                        // Format the date(s)
                        $startDate = new DateTime($activity['start_date']);
                        $endDate = new DateTime($activity['end_date']);
                        
                        $dateDisplay = $startDate->format('d/m/Y');
                        if ($startDate != $endDate) {
                            $dateDisplay .= ' - ' . $endDate->format('d/m/Y');
                        }
                    ?>
                        <tr>
                            <td data-label="No."><?= $counter++ ?></td>
                            <td data-label="Date"><?= htmlspecialchars($dateDisplay) ?></td>
                            <td data-label="Activity"><?= htmlspecialchars($activity['activity_name']) ?></td>
                            <td data-label="Organizer"><?= htmlspecialchars($activity['organizer']) ?></td>
                            <td data-label="Role"><?= htmlspecialchars($activity['role']) ?></td>
                            <td>
                                <button onclick="window.location.href='edit_activity.php?id=<?= $activity['id'] ?>'" class="small-btn btn-blue">Edit</button>
                                <button onclick="deleteActivity(<?= $activity['id'] ?>)" class="small-btn btn-red">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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
            <a href="preview/SLP_preview_<?php echo $start_year ?><?php echo $end_year ?>_<?php echo $stuClass ?><?php echo sprintf("%02d", $stuClassNum) ?>_<?php echo $previewID ?>.pdf" target="_blank">
                <button type="button" class="btn-blue" id="previewpdf">Preview PDF</button>
            </a>
            <button type="button" class="btn-purple" id="submitpdf" onclick="submitPDF()">Submit</button>
        </div>
    </div>
        
    <script src="script/main.js"></script>
    <script src="script/notosanstcedit-normal.js"></script>
</body>
</html>