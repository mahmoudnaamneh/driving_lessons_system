<?php
session_start();
include 'config.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$role_id = $_SESSION['role_id'];
$search_query = '';

// Handle new forum submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['forum_submit'])) {
    $forum_name = $_POST['forum_name'];
    $description = $_POST['description'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO forum (forum_name, description, user_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $forum_name, $description, $user_id);

    if ($stmt->execute()) {
        $message = "New forum created successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Handle new reply submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reply_submit'])) {
    $forum_id = $_POST['forum_id'];
    $reply_text = $_POST['reply_text'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO replies (forum_id, user_id, reply_text) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $forum_id, $user_id, $reply_text);

    if ($stmt->execute()) {
        $message = "Reply added successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch forums based on search query
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['search'])) {
    $search_query = $_GET['search'];
    $forums_stmt = $conn->prepare("
        SELECT 
            forum.forum_id, forum.forum_name, forum.description, forum.created_at, users.username 
        FROM forum 
        JOIN users ON forum.user_id = users.user_id 
        WHERE forum.forum_name LIKE ?
        ORDER BY forum.created_at DESC
    ");
    $search_param = "%" . $search_query . "%";
    $forums_stmt->bind_param("s", $search_param);
} else {
    $forums_stmt = $conn->prepare("
        SELECT 
            forum.forum_id, forum.forum_name, forum.description, forum.created_at, users.username 
        FROM forum 
        JOIN users ON forum.user_id = users.user_id 
        ORDER BY forum.created_at DESC
    ");
}

$forums_stmt->execute();
$forums_result = $forums_stmt->get_result();
$forums = $forums_result->fetch_all(MYSQLI_ASSOC);
$forums_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum</title>
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
<h1>Forum</h1>
<?php if (isset($message)) { echo "<p>$message</p>"; } ?>
<form action="forum.php" method="post">
    <label for="forum_name">Forum Name:</label>
    <input type="text" id="forum_name" name="forum_name" required><br><br>
    
    <label for="description">Description:</label>
    <textarea id="description" name="description" required></textarea><br><br>
    
    <button type="submit" name="forum_submit">Create Forum</button>
</form>

<h2>Search Forums</h2>
<form method="get" action="forum.php">
    <input type="text" name="search" value="<?= htmlspecialchars($search_query) ?>" placeholder="Search by forum name">
    <button type="submit">Search</button>
</form>

<h2>All Forums</h2>
<table border="1">
    <tr>
        <th>Forum Name</th>
        <th>Description</th>
        <th>Created By</th>
        <th>Created At</th>
    </tr>
    <?php
    foreach ($forums as $forum) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($forum['forum_name']) . "</td>";
        echo "<td>" . htmlspecialchars($forum['description']) . "</td>";
        echo "<td>" . htmlspecialchars($forum['username']) . "</td>";
        echo "<td>" . htmlspecialchars($forum['created_at']) . "</td>";
        echo "</tr>";
        
        // Fetch replies for this forum
        $forum_id = $forum['forum_id'];
        $replies_stmt = $conn->prepare("
            SELECT replies.reply_text, replies.created_at, users.username 
            FROM replies 
            JOIN users ON replies.user_id = users.user_id 
            WHERE replies.forum_id = ? 
            ORDER BY replies.created_at DESC
        ");
        $replies_stmt->bind_param("i", $forum_id);
        $replies_stmt->execute();
        $replies_result = $replies_stmt->get_result();
        $replies = $replies_result->fetch_all(MYSQLI_ASSOC);
        $replies_stmt->close();
        
        echo "<tr><td colspan='4'>";
        echo "<h3>Replies:</h3>";
        foreach ($replies as $reply) {
            echo "<p><strong>" . htmlspecialchars($reply['username']) . ":</strong> " . htmlspecialchars($reply['reply_text']) . " <em>at " . htmlspecialchars($reply['created_at']) . "</em></p>";
        }
        
        // Reply form
        echo "<form action='forum.php' method='post'>
                <input type='hidden' name='forum_id' value='" . htmlspecialchars($forum_id) . "'>
                <textarea name='reply_text' required></textarea><br>
                <button type='submit' name='reply_submit'>Reply</button>
              </form>";
        echo "</td></tr>";
    }
    ?>
</table>
</body>
</html>
<?php
$conn->close();
?>
