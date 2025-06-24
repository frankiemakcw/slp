<?php
    require_once 'teacher_getdata.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>SLP Submissions</title>
    <link rel="stylesheet" href="style/teacher.css">
</head>
<body>
    <div class="container">
        <h1>SLP Submissions</h1>
        
        <div class="filter-form">
            <form method="get">
                <div class="filter-row">
                    <label for="class">Filter by Class:</label>
                    <select name="class" id="class">
                        <option value="all">All Classes</option>
                        <?php
                        // Get distinct classes for dropdown
                        $classStmt = $pdo->query("SELECT DISTINCT class FROM student ORDER BY class");
                        $classes = $classStmt->fetchAll(PDO::FETCH_COLUMN);
                        
                        foreach ($classes as $class) {
                            $selected = ($class == $selectedClass) ? 'selected' : '';
                            echo "<option value='$class' $selected>$class</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="filter-row">
                    <label for="status">Submission Status:</label>
                    <select name="status" id="status">
                        <option value="all" <?= $submissionStatus === 'all' ? 'selected' : '' ?>>All Students</option>
                        <option value="submitted" <?= $submissionStatus === 'submitted' ? 'selected' : '' ?>>Submitted</option>
                        <option value="not_submitted" <?= $submissionStatus === 'not_submitted' ? 'selected' : '' ?>>Not Submitted</option>
                    </select>
                </div>
                
                <input type="hidden" name="sort" id="sort" value="<?= isset($_GET['sort']) ? $_GET['sort'] : '' ?>">
            </form>
        </div>
        
        <?php if (!empty($records)): ?>
            <div class="action-buttons">
                <?php if ($submissionStatus === 'submitted'): ?>
                    <button id="downloadAll" class="btn">Download All Submitted Files</button>
                <?php endif; ?>
                <?php if ($submissionStatus === 'submitted'): ?>
                    <button id="sortByTime" class="btn">
                        <?= (isset($_GET['sort']) && $_GET['sort'] === 'time_desc') ? 'Sort by Class Number' : 'Sort by Submission Time' ?>
                    </button>
                <?php endif; ?>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Class</th>
                        <th>Class Number</th>
                        <th>Name</th>
                        <th>File</th>
                        <th>Submitted At</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['class']) ?></td>
                            <td><?= htmlspecialchars($row['class_num']) ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td>
                                <?php if (!empty($row['file_path'])): ?>
                                    <a href="<?= htmlspecialchars($row['file_path']) ?>" class="file-link" target="_blank">
                                        View File
                                    </a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?= !empty($row['submitted_at']) ? htmlspecialchars($row['submitted_at']) : '-' ?></td>
                            <td class="<?= empty($row['file_path']) ? 'not-submitted' : '' ?>">
                                <?= empty($row['file_path']) ? 'Not Submitted' : 'Submitted' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-records">
                No students found for the selected criteria.
            </div>
        <?php endif; ?>
    </div>
    <script src="script/teacher.js"></script>
</body>
</html>