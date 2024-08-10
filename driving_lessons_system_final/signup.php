<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $role_id = isset($_POST['role']) ? $_POST['role'] : 3; // Default role_id for students

    // Additional field for teacher role
    $drivingLicenseNumber = isset($_POST['drivingLicenseNumber']) ? $_POST['drivingLicenseNumber'] : '';

    // Check if role is teacher
    if ($role_id == 2) {
        // Validate and process driving license number
        // For simplicity, let's assume it's a required field for teachers
        if (empty($drivingLicenseNumber)) {
            echo "Error: Driving license number is required for teachers.";
            exit; // You can handle this error more gracefully as per your application flow
        }
    }

    $sql = "INSERT INTO users (username, password, full_name, email, role_id, driving_license_number, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssssis", $username, $password, $fullName, $email, $role_id, $drivingLicenseNumber);
        if ($stmt->execute()) {
            echo "<script>alert('Registration successful! Please wait for approval.'); window.location.href='login.php';</script>";
        } else {
            echo "Error: " . $sql . "<br>" . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="login.php">Login</a></li>
            <li><a href="signup.php">Sign Up</a></li>
        </ul>
    </nav>
    <h2>Sign Up</h2>
    <form method="post" action="">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <label for="fullName">Full Name:</label>
        <input type="text" id="fullName" name="fullName" required><br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>
        
        <!-- Role selection -->
        <label for="role">Role:</label>
        <select name="role" id="role" required>
            <option value="3">Student</option>
            <option value="2">Teacher</option>
        </select><br>

        <!-- Conditional field for teacher -->
        <div id="drivingLicenseNumberField" style="display: none;">
            <label for="drivingLicenseNumber">Driving Teacher License Number:</label>
            <input type="text" id="drivingLicenseNumber" name="drivingLicenseNumber"><br>
        </div>

        <button type="submit">Sign Up</button>
    </form>

    <script>
        // Show/hide driving license number field based on role selection
        document.getElementById('role').addEventListener('change', function() {
            var role = this.value;
            var drivingLicenseField = document.getElementById('drivingLicenseNumberField');
            if (role == 2) {
                drivingLicenseField.style.display = 'block';
                document.getElementById('drivingLicenseNumber').required = true;
            } else {
                drivingLicenseField.style.display = 'none';
                document.getElementById('drivingLicenseNumber').required = false;
            }
        });
    </script>
</body>
</html>
