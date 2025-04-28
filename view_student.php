<?php
$connection = new mysqli("localhost", "root", "", "student_information_system");

if (!isset($_GET['student_id'])) {
    header("Location: students.php");
    exit;
}

$student_id = $connection->real_escape_string($_GET['student_id']);

// Get student details
$student_sql = "SELECT * FROM Students WHERE student_id = '$student_id'";
$student_result = $connection->query($student_sql);
$student = $student_result->fetch_assoc();

if (!$student) {
    header("Location: students.php");
    exit;
}

// Get enrollments
$enrollment_sql = "SELECT e.enrollment_id, c.course_id, c.course_name, e.enrollment_date, e.grade, e.status
                   FROM Enrollments e
                   JOIN Courses c ON e.course_id = c.course_id
                   WHERE e.student_id = '$student_id'
                   ORDER BY e.enrollment_date DESC";
$enrollment_result = $connection->query($enrollment_sql);

// Get available courses for enrollment
$available_courses_sql = "SELECT c.course_id, c.course_name 
                          FROM Courses c
                          WHERE c.course_id NOT IN (
                              SELECT course_id FROM Enrollments WHERE student_id = '$student_id' AND status != 'Dropped'
                          )";
$available_courses_result = $connection->query($available_courses_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="bi bi-person"></i> 
                <?php echo "{$student['first_name']} {$student['last_name']} (ID: {$student['student_id']})"; ?>
            </h2>
            <div>
                <a href="edit_student.php?student_id=<?php echo $student_id; ?>" class="btn btn-primary me-2">
                    <i class="bi bi-pencil"></i> Edit Profile
                </a>
                <a href="students.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Students
                </a>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Personal Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Student ID:</div>
                            <div class="col-md-8"><?php echo $student['student_id']; ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Full Name:</div>
                            <div class="col-md-8"><?php echo "{$student['first_name']} {$student['last_name']}"; ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Date of Birth:</div>
                            <div class="col-md-8"><?php echo $student['date_of_birth'] ? date('F j, Y', strtotime($student['date_of_birth'])) : 'N/A'; ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Gender:</div>
                            <div class="col-md-8"><?php echo $student['gender'] ?? 'N/A'; ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Email:</div>
                            <div class="col-md-8"><?php echo $student['email'] ?? 'N/A'; ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Phone:</div>
                            <div class="col-md-8"><?php echo $student['phone'] ?? 'N/A'; ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Address:</div>
                            <div class="col-md-8"><?php echo $student['address'] ?? 'N/A'; ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Admission Date:</div>
                            <div class="col-md-8"><?php echo date('F j, Y', strtotime($student['admission_date'])); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">Course Enrollments</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($enrollment_result->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Course</th>
                                            <th>Date</th>
                                            <th>Grade</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($enrollment = $enrollment_result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo "{$enrollment['course_id']} - {$enrollment['course_name']}"; ?></td>
                                                <td><?php echo date('m/d/Y', strtotime($enrollment['enrollment_date'])); ?></td>
                                                <td><?php echo $enrollment['grade'] ?? 'N/A'; ?></td>
                                                <td>
                                                    <span class="badge 
                                                        <?php echo $enrollment['status'] == 'Active' ? 'bg-primary' : 
                                                              ($enrollment['status'] == 'Completed' ? 'bg-success' : 'bg-secondary'); ?>">
                                                        <?php echo $enrollment['status']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="edit_enroll.php?enrollment_id=<?php echo $enrollment['enrollment_id']; ?>" 
                                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <a href="unenroll.php?enrollment_id=<?php echo $enrollment['enrollment_id']; ?>&student_id=<?php echo $student_id; ?>" 
                                                           class="btn btn-sm btn-outline-danger" title="Unenroll">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">This student is not enrolled in any courses.</div>
                        <?php endif; ?>
                        
                        <!-- Enrollment Form -->
                        <div class="mt-4">
                            <h6>Enroll in New Course</h6>
                            <form method="post" action="enroll.php">
                                <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
                                <div class="input-group mb-3">
                                    <select class="form-select" name="course_id" required>
                                        <option value="">Select Course</option>
                                        <?php while ($course = $available_courses_result->fetch_assoc()): ?>
                                            <option value="<?php echo $course['course_id']; ?>">
                                                <?php echo "{$course['course_id']} - {$course['course_name']}"; ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                    <button type="submit" class="btn btn-success">Enroll</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>