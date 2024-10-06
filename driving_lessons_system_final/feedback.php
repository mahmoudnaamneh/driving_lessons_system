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

// Handle feedback submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $role_id != 1) { // Only users who are not admins can submit feedback
    $feedback_text = $_POST['feedback_text'];

    $sql = "INSERT INTO feedback (user_id, feedback_text) VALUES (?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('is', $user_id, $feedback_text);
        if ($stmt->execute()) {
            $message = "Feedback submitted successfully.";
        } else {
            $message = "Error: Could not execute the query.";
        }
        $stmt->close();
    } else {
        $message = "Error: Could not prepare the query.";
    }
}

// Fetch feedbacks based on user role
if ($role_id == 1) { // Admin view - fetch all feedbacks
    $sql = "SELECT feedback_text, feedback_date, users.username FROM feedback JOIN users ON feedback.user_id = users.user_id ORDER BY feedback.feedback_date DESC";
} else { // User view - fetch only their own feedbacks
    $sql = "SELECT feedback_text, feedback_date, users.username FROM feedback JOIN users ON feedback.user_id = users.user_id WHERE feedback.user_id = ? ORDER BY feedback.feedback_date DESC";
}

if ($stmt = $conn->prepare($sql)) {
    if ($role_id != 1) { // Bind user_id for non-admin users
        $stmt->bind_param('i', $user_id);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    $result = false;
    echo "Error: Could not prepare the query.";
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Feedback</title>
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
<h1>Submit Feedback</h1>
<?php if ($role_id != 1): // Display feedback form for non-admin users ?>
    <form action="feedback.php" method="post">
        <label for="feedback_text">Feedback:</label>
        <textarea name="feedback_text" required></textarea><br><br>
        <button type="submit">Submit</button>
    </form>
<?php endif; ?>

<h1>All Feedback</h1>
<table border="1">
    <tr>
        <th>Feedback</th>
        <th>Submitted By</th>
        <th>Date</th>
    </tr>
    <?php
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['feedback_text']}</td>
                    <td>{$row['username']}</td>
                    <td>{$row['feedback_date']}</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='3'>No feedback found.</td></tr>";
    }
    ?>
</table>
</body>
</html>
