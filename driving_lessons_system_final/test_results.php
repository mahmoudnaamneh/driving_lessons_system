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

// Process form submission for teachers only
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $role_id == 2) {
    $test_id = $_POST['test_id'];
    $user_id = $_POST['user_id'];
    $result = $_POST['result'];
    $test_date = $_POST['test_date'];

    // Fetch the test date for the selected test ID
    $stmt = $conn->prepare("SELECT test_date FROM tests WHERE test_id = ?");
    $stmt->bind_param("i", $test_id);
    $stmt->execute();
    $stmt->bind_result($scheduled_test_date);
    $stmt->fetch();
    $stmt->close();

    $current_date = new DateTime();
    $test_date_obj = new DateTime($scheduled_test_date);

    if ($test_date_obj < $current_date) {
        $stmt = $conn->prepare("INSERT INTO testresults (test_id, user_id, result, test_date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $test_id, $user_id, $result, $test_date);

        if ($stmt->execute()) {
            echo "New test result added successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "The test date has not yet expired. You cannot add a result for this test.";
    }
}

// Fetch test results based on user role
if ($role_id == 3) { // Student
    $stmt = $conn->prepare("
        SELECT 
            tr.result_id, 
            tr.result, 
            tr.test_date, 
            t.test_name 
        FROM 
            testresults tr
        JOIN 
            tests t ON tr.test_id = t.test_id
        WHERE 
            tr.user_id = ?
        ORDER BY 
            tr.test_date DESC");
    $stmt->bind_param("i", $user_id);
} else if ($role_id == 2) { // Teacher
    $stmt = $conn->prepare("
        SELECT 
            tr.result_id, 
            tr.result, 
            tr.test_date, 
            t.test_name, 
            u.username 
        FROM 
            testresults tr
        JOIN 
            tests t ON tr.test_id = t.test_id
        JOIN 
            users u ON tr.user_id = u.user_id
        WHERE 
            t.user_id = ?
        ORDER BY 
            tr.test_date DESC");
    $stmt->bind_param("i", $user_id);
} else if ($role_id == 1) { // Admin
    $stmt = $conn->prepare("
        SELECT 
            tr.result_id, 
            tr.result, 
            tr.test_date, 
            t.test_name, 
            u.username 
        FROM 
            testresults tr
        JOIN 
            tests t ON tr.test_id = t.test_id
        JOIN 
            users u ON tr.user_id = u.user_id
        ORDER BY 
            tr.test_date DESC");
}

$stmt->execute();
$result = $stmt->get_result();

// Fetch tests and users for the form (only for teachers)
if ($role_id == 2) {
    $tests = $conn->query("SELECT test_id, test_name FROM tests WHERE user_id = $user_id");
    $users = $conn->query("SELECT user_id, username FROM users WHERE role_id = 3");
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Test Results</title>
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
    <h1>Test Results</h1>
</header>
<div class="container">
    <?php if ($role_id == 2): ?>
        <h2>Add New Test Result</h2>
        <form method="post" action="">
            <label for="test_id">Test Title:</label>
            <select name="test_id" id="test_id" required>
                <?php while($test = $tests->fetch_assoc()): ?>
                    <option value="<?= $test['test_id'] ?>"><?= $test['test_name'] ?></option>
                <?php endwhile; ?>
            </select><br>

            <label for="user_id">Student Name:</label>
            <select name="user_id" id="user_id" required>
                <?php while($user = $users->fetch_assoc()): ?>
                    <option value="<?= $user['user_id'] ?>"><?= $user['username'] ?></option>
                <?php endwhile; ?>
            </select><br>

            <label for="result">Result:</label>
            <select name="result" id="result" required>
                <option value="Pass">Pass</option>
                <option value="Fail">Fail</option>
            </select><br>

            <label for="test_date">Test Date:</label>
            <input type="date" name="test_date" id="test_date" required><br>

            <input type="submit" value="Add Result">
        </form>
    <?php endif; ?>

    <h2><?php echo ($role_id == 2 || $role_id == 1) ? 'All Test Results' : 'Your Test Results'; ?></h2>
    <table>
        <tr>
            <th>Test Name</th>
            <?php if ($role_id == 2 || $role_id == 1): ?>
                <th>Student Name</th>
            <?php endif; ?>
            <th>Result</th>
            <th>Test Date</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['test_name']) . "</td>";
                if ($role_id == 2 || $role_id == 1) {
                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                }
                echo "<td>" . htmlspecialchars($row['result']) . "</td>";
                echo "<td>" . htmlspecialchars($row['test_date']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='" . (($role_id == 2 || $role_id == 1) ? 4 : 3) . "'>No test results found.</td></tr>";
        }
        ?>
    </table>
</div>
</body>
</html>
