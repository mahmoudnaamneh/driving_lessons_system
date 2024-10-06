<?php
session_start();
include('config.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role_id = $_SESSION['role_id'];
$username = $_SESSION['username'];

// Handle report submission for teachers only
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $role_id == 2) {
    $report_text = $_POST['report_text'];
    $student_name = $_POST['student_name'];
    $teacher_name = $username;

    $sql = "INSERT INTO reports (user_id, report_text, report_date, student_name, teacher_name) VALUES (?, ?, CURDATE(), ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('isss', $user_id, $report_text, $student_name, $teacher_name);
        if ($stmt->execute()) {
            $message = "Report submitted successfully.";
        } else {
            $message = "Error: Could not execute the query.";
        }
        $stmt->close();
    } else {
        $message = "Error: Could not prepare the query.";
    }
}

// Fetch reports based on user role
if ($role_id == 3) { // Student
    $sql = "SELECT report_text, report_date, student_name, teacher_name FROM reports WHERE student_name = ? ORDER BY report_date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
} else if ($role_id == 2) { // Teacher
    $sql = "SELECT report_text, report_date, student_name, teacher_name FROM reports WHERE teacher_name = ? ORDER BY report_date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
} else { // Admin (if applicable)
    $sql = "SELECT report_text, report_date, student_name, teacher_name FROM reports ORDER BY report_date DESC";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();
$reports = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch list of students for teacher's report form
if ($role_id == 2) {
    $students_sql = "SELECT username FROM users WHERE role_id = 3";
    $students_result = $conn->query($students_sql);
    $students = $students_result->fetch_all(MYSQLI_ASSOC);
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reports</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
<ul>
<li><a href="dashboard.php">Dashboard</a></li>
            <?php if ($role_id != 3):?>
                <li><a href="schedule_lesson.php">Lessons</a></li>
            <?php endif; ?>  
            <?php if ($role_id != 3):?>  
            <li><a href="schedule_test.php">Tests</a></li>
            <li><a href="test_results.php">Test Results</a></li>
            <?php endif; ?>
            <?php if ($role_id != 1 && $role_id != 2): // Hide for admin ?>
                <li><a href="student_lessons_progress.php">student lessons&progress</a></li>
            <?php endif; ?>
            <?php if ($role_id != 1 && $role_id != 2): // Hide for admin ?>
                <li><a href="student_tests_results.php">student tests&results</a></li>
            <?php endif; ?>
            <?php if ($role_id != 1 && $role_id != 3): // Hide for admin ?>
                <li><a href="progress.php">Progress</a></li>
            <?php endif; ?>
            <li><a href="forum.php">Forums</a></li>
            <li><a href="upload_materials.php">Materials</a></li>
            <?php if ($role_id != 1): // Hide for admin ?>
                <li><a href="reports.php">Reports</a></li>
            <?php endif; ?>
            <li><a href="feedback.php">Feedback</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
</nav>
<h1>Submit Report</h1>
<?php if (isset($message)) { echo "<p>$message</p>"; } ?>
<?php if ($role_id == 2): // Only show report form to teachers ?>
    <form action="reports.php" method="post">
        <label for="report_text">Report:</label>
        <textarea name="report_text" required></textarea><br><br>
        
        <label for="student_name">Student Name:</label>
        <select name="student_name" required>
            <?php
            foreach ($students as $student) {
                echo "<option value='" . htmlspecialchars($student['username']) . "'>" . htmlspecialchars($student['username']) . "</option>";
            }
            ?>
        </select><br><br>
        
        <button type="submit">Submit</button>
    </form>
<?php endif; ?>

<h1>All Reports</h1>
<table border="1">
    <tr>
        <th>Report</th>
        <th>Student Name</th>
        <th>Teacher Name</th>
        <th>Date</th>
    </tr>
    <?php
    if (!empty($reports)) {
        foreach ($reports as $report) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($report['report_text']) . "</td>";
            echo "<td>" . htmlspecialchars($report['student_name']) . "</td>";
            echo "<td>" . htmlspecialchars($report['teacher_name']) . "</td>";
            echo "<td>" . htmlspecialchars($report['report_date']) . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No reports found.</td></tr>";
    }
    ?>
</table>
</body>
</html>
