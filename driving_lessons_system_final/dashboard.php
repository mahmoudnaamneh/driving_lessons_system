<?php
session_start();
include('config.php');

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$role_id = $_SESSION['role_id'];

// Fetch grade data for the logged-in student if the user is a student
if ($role_id == 3) {
    $stmt = $conn->prepare("SELECT progress_date, grade FROM progress WHERE user_id = ? ORDER BY progress_date ASC");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $grades = [];
        $dates = [];

        while ($row = $result->fetch_assoc()) {
            $grades[] = $row['grade'];
            $dates[] = $row['progress_date'];
        }

        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}

// Fetch lesson data grouped by location for the admin
if ($role_id == 1) {
    $lesson_stmt = $conn->prepare("SELECT location, COUNT(*) as lesson_count FROM lessons GROUP BY location");
    if ($lesson_stmt) {
        $lesson_stmt->execute();
        $lesson_result = $lesson_stmt->get_result();
        $locations = [];
        $lesson_counts = [];

        while ($row = $lesson_result->fetch_assoc()) {
            $locations[] = $row['location'];
            $lesson_counts[] = $row['lesson_count'];
        }

        $lesson_stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    // Fetch teacher-student lesson data for the admin
    $teacher_stmt = $conn->prepare("SELECT teacher_name, COUNT(DISTINCT student_name) as student_count FROM lessons GROUP BY teacher_name");
    if ($teacher_stmt) {
        $teacher_stmt->execute();
        $teacher_result = $teacher_stmt->get_result();
        $teachers = [];
        $student_counts = [];

        while ($row = $teacher_result->fetch_assoc()) {
            $teachers[] = $row['teacher_name'];
            $student_counts[] = $row['student_count'];
        }

        $teacher_stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    // Fetch the most expensive teacher
    $stmt = $conn->prepare("SELECT teacher_name, AVG(price) as avg_price FROM lessons GROUP BY teacher_name ORDER BY avg_price DESC LIMIT 1");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        $most_expensive_teacher = $result->fetch_assoc();
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    // Determine the most famous teacher
    $max_students = max($student_counts);
    $most_famous_teacher_index = array_search($max_students, $student_counts);
    $most_famous_teacher = $teachers[$most_famous_teacher_index];
}

// Fetch total lesson count for the logged-in teacher if the user is a teacher
if ($role_id == 2) {
    
    $teacher_stmt = $conn->prepare("SELECT COUNT(*) as total_lessons FROM lessons WHERE user_id = ?");
    if ($teacher_stmt) {
        $teacher_stmt->bind_param("i", $user_id);
        $teacher_stmt->execute();
        $teacher_result = $teacher_stmt->get_result();
        $total_lessons = 0;

        if ($row = $teacher_result->fetch_assoc()) {
            $total_lessons = $row['total_lessons'];
        }

        $teacher_stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    $today = date('Y-m-d');
    $lesson_details_stmt = $conn->prepare("SELECT student_name, lesson_date, location FROM lessons WHERE user_id = ? AND DATE(lesson_date) = ?");
    if ($lesson_details_stmt) {
        $lesson_details_stmt->bind_param("is", $user_id, $today);
        $lesson_details_stmt->execute();
        $lesson_details_result = $lesson_details_stmt->get_result();
        $scheduled_lessons = [];

        while ($row = $lesson_details_result->fetch_assoc()) {
            $scheduled_lessons[] = $row;
        }

        $lesson_details_stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
// Handle the search request for a student's progress
$search_grades = [];
$search_dates = [];
if ($role_id == 2 && isset($_POST['search_student'])) {
    $search_student_name = $_POST['search_student'];
    $search_stmt = $conn->prepare("SELECT progress_date, grade FROM progress WHERE student_name = ? ORDER BY progress_date ASC");
    if ($search_stmt) {
        $search_stmt->bind_param("s", $search_student_name);
        $search_stmt->execute();
        $search_result = $search_stmt->get_result();

        while ($row = $search_result->fetch_assoc()) {
            $search_grades[] = $row['grade'];
            $search_dates[] = $row['progress_date'];
        }

        $search_stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
  }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        #gradeChartContainer, #searchGradeChartContainer, #lessonChartContainer, #teacherChartContainer, #lessonCountChartContainer {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }
        #gradeChart, #searchGradeChart, #lessonChart, #teacherChart, #lessonCountChart {
            width: 100%;
            height: 200px;
        }
        .chart-container {
            display: flex;
            justify-content: space-around;
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
<h2>Welcome to your Dashboard, <?php echo htmlspecialchars($username); ?></h2>
<p>This is your personal space.</p>

<?php if ($role_id == 3): ?>
    <h2>Your Progress Graph</h2>
    <div id="gradeChartContainer">
        <canvas id="gradeChart"></canvas>
    </div>
    <script>
        var ctx = document.getElementById('gradeChart').getContext('2d');
        var gradeChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($dates); ?>,
                datasets: [{
                    label: 'Grades',
                    data: <?php echo json_encode($grades); ?>,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    fill: false
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
<?php endif; ?>

<?php if ($role_id == 1): ?>
    <h2>Admin Dashboard</h2>
    <div class="chart-container">
        <div id="lessonChartContainer">
            <canvas id="lessonChart"></canvas>
        </div>
        <div id="teacherChartContainer">
            <canvas id="teacherChart"></canvas>
        </div>
    </div>

   <center><h3>Most Expensive Teacher: <?php echo htmlspecialchars($most_expensive_teacher['teacher_name']); ?> (Avg. Price: <?php echo htmlspecialchars($most_expensive_teacher['avg_price']); ?>)</h3></center>
   <center><h3>Most Famous Teacher: <?php echo htmlspecialchars($most_famous_teacher); ?></h3></center>

    <script>
        var ctxLesson = document.getElementById('lessonChart').getContext('2d');
        var lessonChart = new Chart(ctxLesson, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($locations); ?>,
                datasets: [{
                    label: 'Lessons by Location',
                    data: <?php echo json_encode($lesson_counts); ?>,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    },
                    title: {
                        display: true,
                        text: 'Lessons by Location'
                    }
                }
            }
        });

        var ctxTeacher = document.getElementById('teacherChart').getContext('2d');
        var teacherChart = new Chart(ctxTeacher, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($teachers); ?>,
                datasets: [{
                    label: 'Students per Teacher',
                    data: <?php echo json_encode($student_counts); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Students per Teacher'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
<?php endif; ?>

<?php if ($role_id == 2): ?>
    <h2>Your Total Lesson Count</h2>
    <div id="lessonCountChartContainer">
        <canvas id="lessonCountChart"></canvas>
    </div>
    <script>
        var ctxLessonCount = document.getElementById('lessonCountChart').getContext('2d');
        var lessonCountChart = new Chart(ctxLessonCount, {
            type: 'bar',
            data: {
                labels: ['Total Lessons'],
                datasets: [{
                    label: 'Total Lessons',
                    data: [<?php echo $total_lessons; ?>],
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Total Lessons Conducted'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

    <h2>Today's Scheduled Lessons</h2>
    <?php if (!empty($scheduled_lessons)): ?>
        <table border="1">
            <tr>
                <th>Student Name</th>
                <th>Lesson Time</th>
                <th>Location</th>
            </tr>
            <?php foreach ($scheduled_lessons as $lesson): ?>
                <tr>
                    <td><?php echo htmlspecialchars($lesson['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($lesson['lesson_date']); ?></td>
                    <td><?php echo htmlspecialchars($lesson['location']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No lessons scheduled for today.</p>
    <?php endif; ?>

    <h2>Search Student's Progress</h2>
    <form method="POST" action="">
        <label for="search_student">Student Name:</label>
        <input type="text" name="search_student" id="search_student" required>
        <button type="submit">Search</button>
    </form>

    <?php if (!empty($search_grades) && !empty($search_dates)): ?>
        <center><h3>Progress Graph for <?php echo htmlspecialchars($search_student_name); ?></h3></center>
        <div id="searchGradeChartContainer">
            <canvas id="searchGradeChart"></canvas>
        </div>
        <script>
            var searchCtx = document.getElementById('searchGradeChart').getContext('2d');
            var searchGradeChart = new Chart(searchCtx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($search_dates); ?>,
                    datasets: [{
                        label: 'Grades',
                        data: <?php echo json_encode($search_grades); ?>,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderWidth: 1,
                        fill: false
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    },
                    plugins: {
                        legend: {
                            display: true
                        },
                        title: {
                            display: true,
                            text: 'Student Progress'
                        }
                    }
                }
            });
        </script>
    <?php endif; ?>
<?php endif; ?>

</body>
</html>
