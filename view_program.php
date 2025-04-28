<?php
$connection = new mysqli("localhost", "root", "", "student_information_system");

if (!isset($_GET['program_id'])) {
    header("Location: programs.php");
    exit;
}

$program_id = $connection->real_escape_string($_GET['program_id']);

// Get program details
$program_sql = "SELECT p.*, d.dept_name 
                FROM Programs p
                JOIN Departments d ON p.dept_id = d.dept_id
                WHERE p.program_id = '$program_id'";
$program_result = $connection->query($program_sql);
$program = $program_result->fetch_assoc();

if (!$program) {
    header("Location: programs.php");
    exit;
}

// Get program courses
$courses_sql = "SELECT * FROM Courses WHERE program_id = '$program_id' ORDER BY course_name";
$courses_result = $connection->query($courses_sql);

// Get program statistics
$stats_sql = "SELECT 
              (SELECT COUNT(*) FROM Courses WHERE program_id = '$program_id') as course_count,
              (SELECT COUNT(*) FROM Enrollments e JOIN Courses c ON e.course_id = c.course_id 
               WHERE c.program_id = '$program_id' AND e.status = 'Active') as active_students,
              (SELECT COUNT(*) FROM Enrollments e JOIN Courses c ON e.course_id = c.course_id 
               WHERE c.program_id = '$program_id' AND e.status = 'Completed') as completed_students";
$stats_result = $connection->query($stats_sql);
$stats = $stats_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Program Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .program-header {
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
            height: 100%;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .course-badge {
            font-size: 0.9rem;
            margin-right: 5px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container my-5">
        <div class="program-header">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h1><?php echo $program['program_name']; ?></h1>
                    <p class="lead mb-0"><?php echo $program['program_id']; ?></p>
                    <p class="mb-0"><?php echo $program['dept_name']; ?> Department • <?php echo $program['duration_years']; ?> year program</p>
                </div>
                <div>
                    <span class="badge bg-light text-dark fs-6"><?php echo $stats['course_count']; ?> Courses</span>
                </div>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card stats-card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Active Students</h6>
                                <h2 class="mb-0"><?php echo $stats['active_students']; ?></h2>
                            </div>
                            <i class="bi bi-people-fill" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stats-card text-white bg-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Courses Offered</h6>
                                <h2 class="mb-0"><?php echo $stats['course_count']; ?></h2>
                            </div>
                            <i class="bi bi-book-half" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stats-card text-white bg-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Graduates</h6>
                                <h2 class="mb-0"><?php echo $stats['completed_students']; ?></h2>
                            </div>
                            <i class="bi bi-mortarboard-fill" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Program Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Program ID:</strong> <?php echo $program['program_id']; ?></p>
                                <p><strong>Department:</strong> <?php echo $program['dept_name']; ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Duration:</strong> <?php echo $program['duration_years']; ?> years</p>
                                <p><strong>Established:</strong> 2015</p>
                            </div>
                        </div>
                        
                        <h5 class="mt-4">Description</h5>
                        <p><?php echo $program['description'] ?? 'No description available.'; ?></p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Program Courses</h5>
                            <a href="create_course.php?program_id=<?php echo $program_id; ?>" class="btn btn-sm btn-light">
                                <i class="bi bi-plus"></i> Add Course
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if ($courses_result->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Course Code</th>
                                            <th>Course Name</th>
                                            <th>Credits</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($course = $courses_result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $course['course_id']; ?></td>
                                                <td><?php echo $course['course_name']; ?></td>
                                                <td><?php echo $course['credit_hours']; ?></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="view_course.php?course_id=<?php echo $course['course_id']; ?>" 
                                                           class="btn btn-outline-primary" title="View">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="edit_course.php?course_id=<?php echo $course['course_id']; ?>" 
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
                            <div class="alert alert-info">No courses currently offered in this program.</div>
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
                            <a href="edit_program.php?program_id=<?php echo $program_id; ?>" class="btn btn-primary">
                                <i class="bi bi-pencil"></i> Edit Program
                            </a>
                            <a href="view_department.php?dept_id=<?php echo $program['dept_id']; ?>" class="btn btn-outline-primary">
                                <i class="bi bi-building"></i> View Department
                            </a>
                            <a href="#" class="btn btn-outline-success">
                                <i class="bi bi-file-earmark-text"></i> Program Handbook
                            </a>
                            <a href="#" class="btn btn-outline-info">
                                <i class="bi bi-diagram-3"></i> Curriculum Map
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0">Program Requirements</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Core Courses
                                <span class="badge bg-primary rounded-pill">12</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Electives
                                <span class="badge bg-primary rounded-pill">4</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Total Credits Required
                                <span class="badge bg-success rounded-pill">120</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Minimum GPA
                                <span class="badge bg-info rounded-pill">2.0</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>