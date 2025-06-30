<?php
    require_once 'getdata.php';
    if ((substr($stuClass, 0, 1) === '1')) {
        $pdfPath = 'calendar/calendar_s1.pdf';
    } else {
        $pdfPath = 'calendar/calendar_all.pdf';
    }
    if ($is_submitted==false) {
        header("Location: main.php");
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
                            <td><?= $counter++ ?>.</td>
                            <td><?= htmlspecialchars($dateDisplay) ?></td>
                            <td><?= htmlspecialchars($activity['activity_name']) ?></td>
                            <td><?= htmlspecialchars($activity['organizer']) ?></td>
                            <td><?= htmlspecialchars($activity['role']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p id="no-records-message">No activities saved yet.</p>
        <?php endif; ?>
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
        </div>
    <?php endif; ?>

    <div class="container">
        <p style="text-align: center;">Submitted at <?= htmlspecialchars($submissionTime) ?>.</p>
        <p style="text-align: center;">You are advised to download the submission for your records.</p>
        <p style="text-align: center;">If you would like to edit your SLP, please discard the submission.</p>
        <div class="form-actions">
            <a href="<?php echo $filepath; ?>"  target="_blank">
                <button class="btn-blue">View Submission</button>
            </a>
            <button type="button" class="btn-red" id="discardsubmission">Discard Submission</button>
        </div>
    </div>

    <script src="script/submitted.js"></script>
</body>
</html>