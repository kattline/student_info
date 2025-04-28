<?php
$connection = new mysqli("localhost", "root", "", "student_information_system");

if (!isset($_GET['course_id'])) {
    header("Location: courses.php");
    exit;
}

$course_id = $connection->real_escape_string($_GET['course_id']);

// Get course details
$course_sql = "SELECT c.*, p.program_name, d.dept_name 
               FROM Courses c
               JOIN Programs p ON c.program_id = p.program_id
               JOIN Departments d ON p.dept_id = d.dept_id
               WHERE c.course_id = '$course_id'";
$course_result = $connection->query($course_sql);
$course = $course_result->fetch_assoc();

if (!$course) {
    header("Location: courses.php");
    exit;
}

// Get enrolled students
$students_sql = "SELECT e.enrollment_id, s.student_id, CONCAT(s.first_name, ' ', s.last_name) as student_name, 
                e.enrollment_date, e.status, e.grade
                FROM Enrollments e
                JOIN Students s ON e.student_id = s.student_id
                WHERE e.course_id = '$course_id'
                ORDER BY e.status, student_name";
$students_result = $connection->query($students_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .course-header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .stats-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container my-5">
        <div class="course-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><?php echo $course['course_name']; ?></h1>
                    <p class="lead mb-0"><?php echo $course['course_id']; ?></p>
                    <p class="mb-0"><?php echo $course['program_name']; ?> • <?php echo $course['dept_name']; ?></p>
                </div>
                <div class="text-end">
                    <span class="badge bg-light text-dark fs-6"><?php echo $course['credit_hours']; ?> Credits</span>
                </div>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card stats-card text-white bg-primary mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Enrolled Students</h6>
                                <h2 class="mb-0"><?php echo $students_result->num_rows; ?></h2>
                            </div>
                            <i class="bi bi-people-fill" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stats-card text-white bg-success mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Active Students</h6>
                                <h2 class="mb-0">
                                    <?php 
                                        $active = $connection->query("SELECT COUNT(*) FROM Enrollments 
                                                                     WHERE course_id = '$course_id' AND status = 'Active'");
                                        echo $active->fetch_row()[0];
                                    ?>
                                </h2>
                            </div>
                            <i class="bi bi-check-circle-fill" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stats-card text-white bg-info mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Average Grade</h6>
                                <h2 class="mb-0">B+</h2>
                            </div>
                            <i class="bi bi-award-fill" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Course Description</h5>
                    </div>
                    <div class="card-body">
                        <p><?php echo $course['description'] ?? 'No description available.'; ?></p>
                        
                        <div class="accordion mt-4" id="courseDetailsAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                        <i class="bi bi-info-circle me-2"></i> Course Details
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#courseDetailsAccordion">
                                    <div class="accordion-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Department:</strong> <?php echo $course['dept_name']; ?></p>
                                                <p><strong>Program:</strong> <?php echo $course['program_name']; ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Credit Hours:</strong> <?php echo $course['credit_hours']; ?></p>
                                                <p><strong>Course Code:</strong> <?php echo $course['course_id']; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Enrolled Students</h5>
                            <a href="enroll_student.php?course_id=<?php echo $course_id; ?>" class="btn btn-sm btn-light">
                                <i class="bi bi-plus"></i> Enroll Student
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if ($students_result->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Student ID</th>
                                            <th>Name</th>
                                            <th>Status</th>
                                            <th>Grade</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($student = $students_result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $student['student_id']; ?></td>
                                                <td><?php echo $student['student_name']; ?></td>
                                                <td>
                                                    <span class="badge <?php 
                                                        echo $student['status'] == 'Active' ? 'bg-primary' : 
                                                             ($student['status'] == 'Completed' ? 'bg-success' : 'bg-secondary');
                                                    ?>">
                                                        <?php echo $student['status']; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo $student['grade'] ?? 'N/A'; ?></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="view_student.php?student_id=<?php echo $student['student_id']; ?>" 
                                                           class="btn btn-outline-primary" title="View">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="edit_enroll.php?enrollment_id=<?php 
                                                            // You would need to get the enrollment_id here
                                                            echo $student['enrollment_id'];
                                                        ?>" 
                                                           class="btn btn-outline-success" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">No students are currently enrolled in this course.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="edit_course.php?course_id=<?php echo $course_id; ?>" class="btn btn-primary">
                                <i class="bi bi-pencil"></i> Edit Course
                            </a>
                            <a href="#" class="btn btn-outline-primary">
                                <i class="bi bi-file-earmark-text"></i> View Syllabus
                            </a>
                            <a href="#" class="btn btn-outline-success">
                                <i class="bi bi-journal-text"></i> Course Materials
                            </a>
                            <a href="#" class="btn btn-outline-info">
                                <i class="bi bi-calendar"></i> Schedule
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0">Grade Distribution</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="gradeChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Grade distribution chart
        const ctx = document.getElementById('gradeChart').getContext('2d');
        const gradeChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['A', 'B', 'C', 'D', 'F'],
                datasets: [{
                    label: 'Students',
                    data: [12, 19, 8, 3, 2],
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.7)',
                        'rgba(13, 110, 253, 0.7)',
                        'rgba(255, 193, 7, 0.7)',
                        'rgba(253, 126, 20, 0.7)',
                        'rgba(220, 53, 69, 0.7)'
                    ],
                    borderColor: [
                        'rgba(40, 167, 69, 1)',
                        'rgba(13, 110, 253, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(253, 126, 20, 1)',
                        'rgba(220, 53, 69, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>