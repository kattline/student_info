<?php
$connection = new mysqli("localhost", "root", "", "student_information_system");

if (!isset($_GET['dept_id'])) {
    header("Location: departments.php");
    exit;
}

$dept_id = $connection->real_escape_string($_GET['dept_id']);

// Get department details
$dept_sql = "SELECT * FROM Departments WHERE dept_id = '$dept_id'";
$dept_result = $connection->query($dept_sql);
$department = $dept_result->fetch_assoc();

if (!$department) {
    header("Location: departments.php");
    exit;
}

// Get department programs
$programs_sql = "SELECT * FROM Programs WHERE dept_id = '$dept_id' ORDER BY program_name";
$programs_result = $connection->query($programs_sql);

// Get department courses (through programs)
$courses_sql = "SELECT c.course_id, c.course_name, p.program_name
                FROM Courses c
                JOIN Programs p ON c.program_id = p.program_id
                WHERE p.dept_id = '$dept_id'
                ORDER BY c.course_name";
$courses_result = $connection->query($courses_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="bi bi-building"></i> 
                <?php echo "{$department['dept_name']} (ID: {$department['dept_id']})"; ?>
            </h2>
            <div>
                <a href="edit_department.php?dept_id=<?php echo $dept_id; ?>" class="btn btn-primary me-2">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                <a href="departments.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Departments
                </a>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Department Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3 fw-bold">Department ID:</div>
                            <div class="col-md-9"><?php echo $department['dept_id']; ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 fw-bold">Department Name:</div>
                            <div class="col-md-9"><?php echo $department['dept_name']; ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 fw-bold">Description:</div>
                            <div class="col-md-9"><?php echo $department['description'] ?? 'N/A'; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Programs</h5>
                            <a href="create_program.php?dept_id=<?php echo $dept_id; ?>" class="btn btn-sm btn-light">
                                <i class="bi bi-plus"></i> Add Program
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if ($programs_result->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Program ID</th>
                                            <th>Program Name</th>
                                            <th>Duration</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($program = $programs_result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $program['program_id']; ?></td>
                                                <td><?php echo $program['program_name']; ?></td>
                                                <td><?php echo $program['duration_years'] . ' years'; ?></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="view_program.php?program_id=<?php echo $program['program_id']; ?>" 
                                                           class="btn btn-sm btn-outline-info" title="View">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="edit_program.php?program_id=<?php echo $program['program_id']; ?>" 
                                                           class="btn btn-sm btn-outline-primary" title="Edit">
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
                            <div class="alert alert-info">This department has no programs yet.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">Courses</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($courses_result->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Course ID</th>
                                            <th>Course Name</th>
                                            <th>Program</th>
                                            <th>Credits</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($course = $courses_result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $course['course_id']; ?></td>
                                                <td><?php echo $course['course_name']; ?></td>
                                                <td><?php echo $course['program_name']; ?></td>
                                                <td><?php echo $course['credit_hours'] ?? 'N/A'; ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">This department has no courses yet.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>