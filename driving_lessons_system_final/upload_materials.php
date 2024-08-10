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

// Handle file upload for teachers only
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload_material']) && $role_id == 2) {
    $material_name = $_POST['material_name'];
    $material_type = $_POST['material_type'];
    
    // Handle file upload
    if (isset($_FILES['material_file'])) {
        $file_error = $_FILES['material_file']['error'];

        if ($file_error === UPLOAD_ERR_OK) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["material_file"]["name"]);

            // Ensure the uploads directory exists
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            if (move_uploaded_file($_FILES["material_file"]["tmp_name"], $target_file)) {
                $stmt = $conn->prepare("INSERT INTO materials (material_name, material_type, material_src, user_id) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("sssi", $material_name, $material_type, $target_file, $user_id);

                if ($stmt->execute()) {
                    $message = "Material uploaded successfully!";
                } else {
                    $message = "Database error: " . $stmt->error;
                }

                $stmt->close();
            } else {
                $message = "Failed to move uploaded file.";
            }
        } else {
            $error_messages = [
                UPLOAD_ERR_INI_SIZE => "The uploaded file exceeds the upload_max_filesize directive in php.ini.",
                UPLOAD_ERR_FORM_SIZE => "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.",
                UPLOAD_ERR_PARTIAL => "The uploaded file was only partially uploaded.",
                UPLOAD_ERR_NO_FILE => "No file was uploaded.",
                UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder.",
                UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk.",
                UPLOAD_ERR_EXTENSION => "File upload stopped by extension."
            ];

            $message = isset($error_messages[$file_error]) ? $error_messages[$file_error] : "Unknown upload error.";
        }
    } else {
        $message = "No file selected or there was an error with the upload.";
    }
}

// Fetch all materials
$materials_stmt = $conn->prepare("SELECT materials.*, users.username FROM materials JOIN users ON materials.user_id = users.user_id ORDER BY created_at DESC");
$materials_stmt->execute();
$materials_result = $materials_stmt->get_result();
$materials = $materials_result->fetch_all(MYSQLI_ASSOC);
$materials_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Materials</title>
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
    <h1>Upload Materials</h1>
    <?php if (isset($message)) { echo "<p>$message</p>"; } ?>
    <?php if ($role_id == 2): // Only show upload form to teachers ?>
        <form action="upload_materials.php" method="post" enctype="multipart/form-data">
            <label for="material_name">Material Name:</label>
            <input type="text" id="material_name" name="material_name" required><br><br>

            <label for="material_type">Material Type:</label>
            <select id="material_type" name="material_type" required>
                <option value="video">Video</option>
                <option value="document">Document</option>
                <option value="question">Question</option>
            </select><br><br>

            <label for="material_file">Upload File:</label>
            <input type="file" id="material_file" name="material_file" required><br><br>

            <button type="submit" name="upload_material">Upload</button>
        </form>
    <?php endif; ?>

    <h2>Uploaded Materials</h2>
    <table border="1">
        <tr>
            <th>Material Name</th>
            <th>Material Type</th>
            <th>Uploaded By</th>
            <th>Uploaded At</th>
            <th>Material</th>
        </tr>
        <?php
        foreach ($materials as $material) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($material['material_name']) . "</td>";
            echo "<td>" . htmlspecialchars($material['material_type']) . "</td>";
            echo "<td>" . htmlspecialchars($material['username']) . "</td>";
            echo "<td>" . htmlspecialchars($material['created_at']) . "</td>";
            echo "<td><a href='" . htmlspecialchars($material['material_src']) . "' target='_blank'>View Material</a></td>";
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>
<?php
$conn->close();
?>
