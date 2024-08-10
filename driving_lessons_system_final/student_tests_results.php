<?php
session_start();
include('config.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role_id = $_SESSION['role_id'];

// Fetch the tests and test results for the logged-in student
$stmt = $conn->prepare("
    SELECT 
        t.test_name, 
        t.test_date, 
        t.location, 
        t.price, 
        t.tester_name,
        tr.result 
    FROM 
        tests t
    LEFT JOIN 
        testresults tr ON t.test_id = tr.test_id 
    WHERE 
        t.student_name = (SELECT username FROM users WHERE user_id = ?)
    ORDER BY 
        t.test_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Tests and Results</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        .fail-row {
            background-color: red;
        }
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
    <h1>Student Tests and Results</h1>
</header>
<div class="container">
    <h2>Your Test Results</h2>
    <table>
        <tr>
            <th>Test Name</th>
            <th>Test Date</th>
            <th>Location</th>
            <th>Price</th>
            <th>Tester Name</th>
            <th>Result</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row_class = ($row['result'] == 'Fail') ? 'fail-row' : '';
                echo "<tr class='$row_class'>
                        <td>" . htmlspecialchars($row['test_name']) . "</td>
                        <td>" . htmlspecialchars($row['test_date']) . "</td>
                        <td>" . htmlspecialchars($row['location']) . "</td>
                        <td>" . htmlspecialchars($row['price']) . "</td>
                        <td>" . htmlspecialchars($row['tester_name']) . "</td>
                        <td>" . htmlspecialchars($row['result']) . "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No test results found.</td></tr>";
        }
        ?>
    </table>
</div>
</body>
</html>
