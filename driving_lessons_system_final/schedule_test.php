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

// Fetch the teacher's name
$teacher_stmt = $conn->prepare("SELECT username FROM users WHERE user_id = ?");
$teacher_stmt->bind_param("i", $user_id);
$teacher_stmt->execute();
$teacher_stmt->bind_result($teacher_name);
$teacher_stmt->fetch();
$teacher_stmt->close();

// Process form submission for teachers only
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $role_id == 2) {
    $test_type = $_POST['test_type'];
    $test_date = $_POST['test_date'];
    $location = $_POST['location'];
    $price = isset($_POST['price']) ? $_POST['price'] : null; // Get the price from the form, allow NULL
    $student_id = $_POST['student_id']; // Get the student ID from the form

    // Fetch the student name based on the selected student ID
    $stmt = $conn->prepare("SELECT username FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt->bind_result($student_name);
    $stmt->fetch();
    $stmt->close();

    $sql = "INSERT INTO tests (test_name, test_date, user_id, location, price, student_name, tester_name) VALUES (?, ?, ?, ?, ?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('ssissss', $test_type, $test_date, $user_id, $location, $price, $student_name, $teacher_name);
        if ($stmt->execute()) {
            echo "Test scheduled successfully.";
        } else {
            echo "Error: Could not execute the query.";
        }
        $stmt->close();
    } else {
        echo "Error: Could not prepare the query.";
    }
}

// Fetch students for the dropdown
$students = $conn->query("SELECT user_id, username FROM users WHERE role_id = 3");

// Fetch scheduled tests based on user role
if ($role_id == 3) { // Student
    $stmt = $conn->prepare("SELECT test_name, test_date, location, price, student_name, tester_name FROM tests WHERE student_name = (SELECT username FROM users WHERE user_id = ?) ORDER BY test_date DESC");
    $stmt->bind_param("i", $user_id);
} else if ($role_id == 2) { // Teacher
    $stmt = $conn->prepare("SELECT test_name, test_date, location, price, student_name, tester_name FROM tests WHERE user_id = ? ORDER BY test_date DESC");
    $stmt->bind_param("i", $user_id);
} else if ($role_id == 1) { // Admin
    $stmt = $conn->prepare("SELECT test_name, test_date, location, price, student_name, tester_name FROM tests ORDER BY test_date DESC");
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
}

$stmt->execute();
$result = $stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Schedule Test</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        .expired {
            background-color: red;
            color: black;
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
    <h1>Schedule Test</h1>
</header>
<div class="container">
    <?php if ($role_id == 2): ?>
        <h2>Schedule a New Test</h2>
        <form action="schedule_test.php" method="post">
            <label for="test_type">Test Title:</label>
            <input type="text" name="test_type" value="driving test"><br><br>
            
            <label for="test_date">Test Date:</label>
            <input type="datetime-local" name="test_date" required><br><br>
            
            <label for="location">Location:</label>
            <select name="location" required>
                <option value="Northern District">Northern District</option>
                <option value="Southern District">Southern District</option>
                <option value="Center District">Center District</option>
                <option value="Haifa District">Haifa District</option>
                <option value="Tel Aviv District">Tel Aviv District</option>
                <option value="Jerusalem District">Jerusalem District</option>
            </select><br><br>
            
            <label for="price">Price:</label>
            <input type="number" name="price" required><br><br>
            
            <label for="student_id">Student Name:</label>
            <select name="student_id" required>
                <?php while($student = $students->fetch_assoc()): ?>
                    <option value="<?= $student['user_id'] ?>"><?= $student['username'] ?></option>
                <?php endwhile; ?>
            </select><br><br>

            <button type="submit">Schedule Test</button>
        </form>
    <?php endif; ?>

    <h2>Scheduled Tests</h2>
    <table>
        <tr>
            <th>Test Type</th>
            <th>Scheduled Date</th>
            <th>Location</th>
            <th>Price</th>
            <th>Student Name</th>
            <th>Tester Name</th>
        </tr>
        <?php
        $current_date = new DateTime();
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $test_date = new DateTime($row['test_date']);
                $expired_class = ($test_date < $current_date) ? 'expired' : '';
                echo "<tr class='$expired_class'>
                        <td>{$row['test_name']}</td>
                        <td>{$row['test_date']}</td>
                        <td>{$row['location']}</td>
                        <td>{$row['price']}</td>
                        <td>{$row['student_name']}</td>
                        <td>{$row['tester_name']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No scheduled tests found.</td></tr>";
        }
        ?>
    </table>
</div>
</body>
</html>
