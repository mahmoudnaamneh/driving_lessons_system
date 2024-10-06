<?php
session_start();
include 'config.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role_id = $_SESSION['role_id'];
$username = $_SESSION['username'];

// Handle form submission for teachers only
if ($_SERVER["REQUEST_METHOD"] == "POST" && $role_id == 2) {
    $lesson_title = $_POST['lesson_title'];
    $lesson_date = $_POST['lesson_date'];
    $price = $_POST['price'];
    $location = $_POST['location'];
    $student_name = $_POST['student_name'];

    // Debugging: Ensure all variables are captured correctly
    error_log("Debug: lesson_title = $lesson_title, lesson_date = $lesson_date, price = $price, location = $location, student_name = $student_name");

    $stmt = $conn->prepare("INSERT INTO lessons (user_id, lesson_title, lesson_date, price, teacher_name, location, student_name) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("ississs", $user_id, $lesson_title, $lesson_date, $price, $username, $location, $student_name);

    // Execute the query
    if ($stmt->execute()) {
        $message = "New lesson scheduled successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Fetch scheduled lessons
$search_query = "";
if ($role_id == 3) { // Student
    $stmt = $conn->prepare("SELECT lesson_title, lesson_date, price, teacher_name, location, student_name FROM lessons WHERE student_name = ? ORDER BY lesson_date DESC");
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("s", $username);
} else if ($role_id == 2) { // Teacher
    $stmt = $conn->prepare("SELECT lesson_title, lesson_date, price, teacher_name, location, student_name FROM lessons WHERE teacher_name = ? ORDER BY lesson_date DESC");
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("s", $username);
} else if ($role_id == 1) { // Admin
    if (isset($_GET['search_query'])) {
        $search_query = $_GET['search_query'];
        $stmt = $conn->prepare("SELECT lesson_title, lesson_date, price, teacher_name, location, student_name FROM lessons WHERE teacher_name LIKE ? ORDER BY lesson_date DESC");
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }
        $search_param = "%" . $search_query . "%";
        $stmt->bind_param("s", $search_param);
    } else {
        $stmt = $conn->prepare("SELECT lesson_title, lesson_date, price, teacher_name, location, student_name FROM lessons ORDER BY lesson_date DESC");
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }
    }
}

$stmt->execute();
$result = $stmt->get_result();
$lessons = $result->fetch_all(MYSQLI_ASSOC);

// Fetch list of students for teacher's lesson scheduling
if ($role_id == 2) {
    $students_stmt = $conn->prepare("SELECT username FROM users WHERE role_id = 3"); // Assuming role_id = 3 is for students
    if ($students_stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    $students_stmt->execute();
    $students_result = $students_stmt->get_result();
    $students = $students_result->fetch_all(MYSQLI_ASSOC);
    $students_stmt->close();
}

// Close the statements and connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Driving Lessons</title>
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
    <h1>Schedule Driving Lesson</h1>
    <?php
    if (isset($message)) {
        echo "<p>$message</p>";
    }
    ?>
    <?php if ($role_id == 2): // Only show form to teachers ?>
        <form action="schedule_lesson.php" method="post">
            <label for="lesson_title">Lesson Title:</label>
            <input type="text" id="lesson_title" name="lesson_title" required><br><br>

            <label for="lesson_date">Lesson Date and Time:</label>
            <input type="datetime-local" id="lesson_date" name="lesson_date" required><br><br>
            
            <label for="location">Location:</label>
            <select id="location" name="location" required>
                <option value="Northern District">Northern District</option>
                <option value="Southern District">Southern District</option>
                <option value="Center District">Center District</option>
                <option value="Haifa District">Haifa District</option>
                <option value="Tel Aviv District">Tel Aviv District</option>
                <option value="Jerusalem District">Jerusalem District</option>
            </select><br><br>

            <label for="price">Price:</label>
            <input type="number" id="price" name="price" required><br><br>

            <label for="student_name">Student:</label>
            <select id="student_name" name="student_name" required>
                <?php
                foreach ($students as $student) {
                    echo "<option value=\"" . htmlspecialchars($student['username']) . "\">" . htmlspecialchars($student['username']) . "</option>";
                }
                ?>
            </select><br><br>
            
            <button type="submit">Schedule Lesson</button>
        </form>
    <?php endif; ?>

    <?php if ($role_id == 1): // Only show search bar to admins ?>
        <form action="schedule_lesson.php" method="get">
            <label for="search_query">Search by Teacher Name:</label>
            <input type="text" id="search_query" name="search_query" value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit">Search</button>
        </form>
    <?php endif; ?>

    <h2>Scheduled Lessons</h2>
    <table border="1">
        <tr>
            <th>Lesson Title</th>
            <th>Lesson Date</th>
            <th>Price</th>
            <th>Teacher Name</th>
            <th>Location</th>
            <th>Student Name</th>
        </tr>
        <?php
        foreach ($lessons as $lesson) {
            // Check if the lesson date is in the past
            $lesson_date = new DateTime($lesson['lesson_date']);
            $now = new DateTime();
            $row_class = ($lesson_date < $now) ? 'class="past-lesson"' : '';

            echo "<tr $row_class>";
            echo "<td>" . htmlspecialchars($lesson['lesson_title']) . "</td>";
            echo "<td>" . htmlspecialchars($lesson['lesson_date']) . "</td>";
            echo "<td>" . htmlspecialchars($lesson['price']) . "</td>";
            echo "<td>" . htmlspecialchars($lesson['teacher_name']) . "</td>";
            echo "<td>" . htmlspecialchars($lesson['location']) . "</td>";
            echo "<td>" . htmlspecialchars($lesson['student_name']) . "</td>";
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>
