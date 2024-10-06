<?php
session_start();
include('config.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) { // Only students can access this page
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$role_id = $_SESSION['role_id'];

// Fetch scheduled lessons for the logged-in student
$lessons_stmt = $conn->prepare("SELECT lesson_title, lesson_date, price, teacher_name, location FROM lessons WHERE student_name = ? ORDER BY lesson_date DESC");
if ($lessons_stmt === false) {
    die("Error preparing statement: " . $conn->error);
}
$lessons_stmt->bind_param("s", $username);
$lessons_stmt->execute();
$lessons_result = $lessons_stmt->get_result();
$lessons = $lessons_result->fetch_all(MYSQLI_ASSOC);
$lessons_stmt->close();

// Fetch progress details for the logged-in student
$progress_stmt = $conn->prepare("SELECT progress_details, progress_date, teacher_name, grade FROM progress WHERE user_id = ? ORDER BY progress_date DESC");
if ($progress_stmt === false) {
    die("Error preparing statement: " . $conn->error);
}
$progress_stmt->bind_param("i", $user_id);
$progress_stmt->execute();
$progress_result = $progress_stmt->get_result();
$progress = $progress_result->fetch_all(MYSQLI_ASSOC);
$progress_stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Lessons and Progress</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
       
    </style>
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
            <li><a href="student_lessons_progress.php">Student Lessons & Progress</a></li>
        <?php endif; ?>
        <?php if ($role_id != 1 && $role_id != 2): // Hide for admin ?>
            <li><a href="student_tests_results.php">Student Tests & Results</a></li>
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
<header>
    <h1>Student Lessons and Progress</h1>
</header>
<div class="container">
    <h2>Scheduled Lessons and Progress</h2>
    <table border="1">
        <tr>
            <th>Lesson Title</th>
            <th>Lesson Date</th>
            <th>Price</th>
            <th>Teacher Name</th>
            <th>Location</th>
            <th>Progress Details</th>
            <th>Progress Date</th>
            <th>Grade</th>
        </tr>
        <?php
        foreach ($lessons as $lesson) {
            $lesson_date = new DateTime($lesson['lesson_date']);
            $now = new DateTime();
            $row_class = ($lesson_date < $now) ? 'class="past-lesson"' : '';

            echo "<tr $row_class>";
            echo "<td>" . htmlspecialchars($lesson['lesson_title']) . "</td>";
            echo "<td>" . htmlspecialchars($lesson['lesson_date']) . "</td>";
            echo "<td>" . htmlspecialchars($lesson['price']) . "</td>";
            echo "<td>" . htmlspecialchars($lesson['teacher_name']) . "</td>";
            echo "<td>" . htmlspecialchars($lesson['location']) . "</td>";

            // Find matching progress details for the lesson
            $matched_progress = array_filter($progress, function($prog) use ($lesson) {
                $prog_date = new DateTime($prog['progress_date']);
                $lesson_date = new DateTime($lesson['lesson_date']);
                return $prog['teacher_name'] == $lesson['teacher_name'] && $prog_date->format('Y-m-d') == $lesson_date->format('Y-m-d');
            });

            if (!empty($matched_progress)) {
                $progress_detail = array_shift($matched_progress);
                echo "<td>" . htmlspecialchars($progress_detail['progress_details']) . "</td>";
                echo "<td>" . htmlspecialchars($progress_detail['progress_date']) . "</td>";
                echo "<td>" . htmlspecialchars($progress_detail['grade']) . "</td>";
            } else {
                echo "<td colspan='3'>No progress recorded</td>";
            }

            echo "</tr>";
        }
        ?>
    </table>
</div>
</body>
</html>
