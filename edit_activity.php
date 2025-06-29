<?php
    require_once 'getdata.php';

    if ($is_submitted) {
        header("Location: submitted.php");
        exit;
    }

    // Verify the activity belongs to the current student
    if (isset($_GET['id'])) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM activity WHERE id = :id AND sid = :sid");
            $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
            $stmt->bindParam(':sid', $sid, PDO::PARAM_STR); 
            $stmt->execute();
            $activity = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$activity) {
                echo "<script>alert('Activity not found or access denied'); window.location.href = 'main.php';</script>";
                exit();
            }
        } catch (PDOException $e) {
            echo "<script>alert('Database error occurred'); window.location.href = 'main.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Invalid activity ID'); window.location.href = 'main.php';</script>";
        exit();
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
        <h2>Edit Activity</h2>
        <form id="eca-form" action="update_activity.php" method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($activity['id']) ?>">
            
            <div class="form-group date-type-container">
                <label class="date-type-label">Date Type:</label>
                <div class="radio-group">
                    <?php
                    $isSingleDate = $activity['start_date'] == $activity['end_date'];
                    ?>
                    <label class="radio-label">
                        <input type="radio" name="date_type" value="single" <?= $isSingleDate ? 'checked' : '' ?>>
                        <span>Single Date</span>
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="date_type" value="duration" <?= !$isSingleDate ? 'checked' : '' ?>>
                        <span>Duration</span>
                    </label>
                </div>
            </div>
            
            <div id="date-input-container">
                <div class="form-group single-date" style="<?= !$isSingleDate ? 'display:none;' : '' ?>">
                    <label for="activity-date">Date</label>
                    <input type="date" id="activity-date" name="activity_date" 
                           value="<?= htmlspecialchars($activity['start_date']) ?>" <?= $isSingleDate ? 'required' : '' ?>>
                </div>
                <div class="form-group duration-date" style="<?= $isSingleDate ? 'display:none;' : '' ?>">
                    <label for="start-date">Start Date</label>
                    <input type="date" id="start-date" name="start_date" 
                           value="<?= htmlspecialchars($activity['start_date']) ?>" <?= !$isSingleDate ? 'required' : '' ?>>
                    <label for="end-date">End Date</label>
                    <input type="date" id="end-date" name="end_date" 
                           value="<?= htmlspecialchars($activity['end_date']) ?>" <?= !$isSingleDate ? 'required' : '' ?>>
                </div>
            </div>
            
            <div class="form-group">
                <label for="activity-name">Activity / Programme / Competition</label>
                <input type="text" id="activity-name" name="activity_name" 
                       value="<?= htmlspecialchars($activity['activity_name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="activity-organizer">Organizer</label>
                <input type="text" id="activity-organizer" name="organizer" list="organizer-list" value="<?= htmlspecialchars($activity['organizer']) ?>" required>
                <datalist id="organizer-list">
                    <option value="Ying Wa Girls' School">
                    <option value="英華女學校">
                    <option value="Hong Kong Schools Music and Speech Association">
                    <option value="香港學校音樂及朗誦協會">
                    <option value="The Schools Sports Federation of Hong Kong, China">
                    <option value="中國香港學界體育聯會">
                </datalist>
            </div>
            <div class="form-group">
                <label for="activity-role">Role</label>
                <input type="text" id="activity-role" name="role" list="role-list" value="<?= htmlspecialchars($activity['role']) ?>"required>
                <datalist id="role-list">
                    <option value="Participant">
                    <option value="參與者">
                    <option value="Performer">
                    <option value="表演者">
                    <option value="Organizer">
                    <option value="籌辦者">
                    <option value="Helper">
                    <option value="工作人員">
                    <option value="Volunteer">
                    <option value="義工">
                </datalist>
            </div>
            <div class="form-actions edit-btn">
                <button type="button" class="btn-red" id="btn-quit" onclick="window.location.href='main.php'">Back</button>
                <button type="submit" class="btn-green">Save</button>
            </div>
        </form>
    </div>

    <script src="script/edit_activity.js"></script>
</body>
</html>



