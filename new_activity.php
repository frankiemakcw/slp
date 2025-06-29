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

    
   
    <div class="container">
        <h2>New Activity</h2>
        <form id="eca-form" action="save_activity.php" method="POST">
            <div class="form-group date-type-container">
                <label class="date-type-label">Date Type:</label>
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="date_type" value="single" checked>
                        <span>Single Date</span>
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="date_type" value="duration">
                        <span>Duration</span>
                    </label>
                </div>
            </div>
            <div id="date-input-container">
                <div class="form-group single-date">
                    <label for="activity-date">Date</label>
                    <input type="date" id="activity-date" name="activity_date" required>
                </div>
                <div class="form-group duration-date" style="display:none;">
                    <label for="start-date">Start Date</label>
                    <input type="date" id="start-date" name="start_date">
                    <label for="end-date">End Date</label>
                    <input type="date" id="end-date" name="end_date">
                </div>
            </div>
            <div class="form-group">
                <label for="activity-name">Activity / Programme / Competition</label>
                <input type="text" id="activity-name" name="activity_name" required>
            </div>
            <div class="form-group">
                <label for="activity-organizer">Organizer</label>
                <input type="text" id="activity-organizer" name="organizer" list="organizer-list" required>
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
                <input type="text" id="activity-role" name="role" list="role-list" required>
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

    <script src="script/new_activity.js"></script>
</body>
</html>