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
$teacher_name = isset($_SESSION['username']) ? $_SESSION['username'] : ''; // Fetch teacher name from session

// Process form submission for teachers only
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $role_id == 2) {
    $student_id = $_POST['student_id'];
    $progress_details = $_POST['progress_details'];
    $progress_date = $_POST['progress_date'];
    $grade = $_POST['grade'];

    // Fetch student name
    $stmt = $conn->prepare("SELECT username FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt->bind_result($student_name);
    $stmt->fetch();
    $stmt->close();

    // Insert progress details
    $stmt = $conn->prepare("INSERT INTO progress (user_id, progress_details, progress_date, teacher_name, student_name, grade) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $student_id, $progress_details, $progress_date, $teacher_name, $student_name, $grade);

    if ($stmt->execute()) {
        echo "Progress recorded successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch progress based on user role
if ($role_id == 3) { // Student
    $stmt = $conn->prepare("SELECT progress_details, progress_date, teacher_name, student_name, grade FROM progress WHERE user_id = ? ORDER BY progress_date DESC");
    $stmt->bind_param("i", $user_id);
} else if ($role_id == 2) { // Teacher
    $stmt = $conn->prepare("SELECT progress_details, progress_date, student_name, grade FROM progress WHERE teacher_name = ? ORDER BY progress_date DESC");
    $stmt->bind_param("s", $teacher_name);
} else if ($role_id == 1) { // Admin
    $stmt = $conn->prepare("SELECT progress_details, progress_date, teacher_name, student_name, grade FROM progress ORDER BY progress_date DESC");
}

$stmt->execute();
$result = $stmt->get_result();

// Fetch students for the dropdown (only for teachers)
if ($role_id == 2) {
    $students_stmt = $conn->prepare("SELECT user_id, username FROM users WHERE role_id = 3");
    if ($students_stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    $students_stmt->execute();
    $students_result = $students_stmt->get_result();
    $students = $students_result->fetch_all(MYSQLI_ASSOC);
    $students_stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Progress</title>
    <link rel="stylesheet" type="text/css" href="style.css">
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
<header>
    <h1>Progress</h1>
</header>
<div class="container">
    <?php if ($role_id == 2): ?>
        <h2>Add New Progress</h2>
        <form method="post" action="">
            <label for="teacher_name">Teacher Name:</label>
            <input type="text" id="teacher_name" name="teacher_name" value="<?= htmlspecialchars($teacher_name) ?>" readonly><br>

            <label for="student_id">Student:</label>
            <select name="student_id" id="student_id" required>
                <?php if (!empty($students)): ?>
                    <?php foreach ($students as $student): ?>
                        <option value="<?= htmlspecialchars($student['user_id']) ?>"><?= htmlspecialchars($student['username']) ?></option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="">No students found</option>
                <?php endif; ?>
            </select><br>

            <label for="progress_details">Progress Details:</label>
            <textarea name="progress_details" id="progress_details" required></textarea><br>

            <label for="progress_date">Progress Date:</label>
            <input type="date" name="progress_date" id="progress_date" required><br>

            <label for="grade">Grade:</label>
            <input type="text" name="grade" id="grade"><br>

            <input type="submit" value="Add Progress">
        </form>
    <?php endif; ?>

    <h2>Progress Details</h2>
    <table>
        <tr>
            <th>Teacher Name</th>
            <th>Student Name</th>
            <th>Progress Details</th>
            <th>Progress Date</th>
            <th>Grade</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . (isset($row['teacher_name']) ? htmlspecialchars($row['teacher_name']) : $teacher_name) . "</td>"; // Display teacher_name
                echo "<td>" . htmlspecialchars($row['student_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['progress_details']) . "</td>";
                echo "<td>" . htmlspecialchars($row['progress_date']) . "</td>";
                echo "<td>" . htmlspecialchars($row['grade']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No progress details found.</td></tr>";
        }
        ?>
    </table>
</div>
</body>
</html>
