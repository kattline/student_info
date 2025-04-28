<?php
$connection = new mysqli("localhost", "root", "", "student_information_system");

if (!isset($_GET['enrollment_id'])) {
    header("Location: enrollments.php");
    exit;
}

$enrollment_id = $connection->real_escape_string($_GET['enrollment_id']);

// Get enrollment details
$sql = "SELECT e.*, s.first_name, s.last_name, c.course_name
        FROM Enrollments e
        JOIN Students s ON e.student_id = s.student_id
        JOIN Courses c ON e.course_id = c.course_id
        WHERE e.enrollment_id = '$enrollment_id'";
$result = $connection->query($sql);
$enrollment = $result->fetch_assoc();

if (!$enrollment) {
    header("Location: enrollments.php");
    exit;
}

$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $grade = $_POST["grade"];
    $status = $_POST["status"];
    
    do {
        if (empty($status)) {
            $errorMessage = "Status is required";
            break;
        }
        
        $update_sql = "UPDATE Enrollments 
                       SET grade = " . (!empty($grade) ? "'$grade'" : "NULL") . ",
                           status = '$status'
                       WHERE enrollment_id = '$enrollment_id'";
        
        $result = $connection->query($update_sql);
        
        if (!$result) {
            $errorMessage = "Invalid query: " . $connection->error;
            break;
        }
        
        $successMessage = "Enrollment updated successfully";
        
        header("location: enrollments.php");
        exit;
    } while (false);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Enrollment</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container my-5">
        <h2><i class="bi bi-pencil"></i> Edit Enrollment</h2>
        
        <?php
        if (!empty($errorMessage)) {
            echo "
            <div class='alert alert-warning alert-dismissible fade show' role='alert'>
                <strong>$errorMessage</strong>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
            ";
        }
        ?>
        
        <form method="post">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Student</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" value="<?php echo "{$enrollment['first_name']} {$enrollment['last_name']} ({$enrollment['student_id']})"; ?>" readonly>
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Course</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" value="<?php echo "{$enrollment['course_name']} ({$enrollment['course_id']})"; ?>" readonly>
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Enrollment Date</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" value="<?php echo date('m/d/Y', strtotime($enrollment['enrollment_date'])); ?>" readonly>
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Grade</label>
                <div class="col-sm-6">
                    <select class="form-select" name="grade">
                        <option value="">Select Grade</option>
                        <option value="A" <?= $enrollment['grade'] == 'A' ? 'selected' : '' ?>>A</option>
                        <option value="A-" <?= $enrollment['grade'] == 'A-' ? 'selected' : '' ?>>A-</option>
                        <option value="B+" <?= $enrollment['grade'] == 'B+' ? 'selected' : '' ?>>B+</option>
                        <option value="B" <?= $enrollment['grade'] == 'B' ? 'selected' : '' ?>>B</option>
                        <option value="B-" <?= $enrollment['grade'] == 'B-' ? 'selected' : '' ?>>B-</option>
                        <option value="C+" <?= $enrollment['grade'] == 'C+' ? 'selected' : '' ?>>C+</option>
                        <option value="C" <?= $enrollment['grade'] == 'C' ? 'selected' : '' ?>>C</option>
                        <option value="C-" <?= $enrollment['grade'] == 'C-' ? 'selected' : '' ?>>C-</option>
                        <option value="D+" <?= $enrollment['grade'] == 'D+' ? 'selected' : '' ?>>D+</option>
                        <option value="D" <?= $enrollment['grade'] == 'D' ? 'selected' : '' ?>>D</option>
                        <option value="F" <?= $enrollment['grade'] == 'F' ? 'selected' : '' ?>>F</option>
                    </select>
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Status*</label>
                <div class="col-sm-6">
                    <select class="form-select" name="status" required>
                        <option value="Active" <?= $enrollment['status'] == 'Active' ? 'selected' : '' ?>>Active</option>
                        <option value="Completed" <?= $enrollment['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="Dropped" <?= $enrollment['status'] == 'Dropped' ? 'selected' : '' ?>>Dropped</option>
                    </select>
                </div>
            </div>
            
            <?php
            if (!empty($successMessage)) {
                echo "
                <div class='row mb-3'>
                    <div class='offset-sm-3 col-sm-6'>
                        <div class='alert alert-success alert-dismissible fade show' role='alert'>
                            <strong>$successMessage</strong>
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                        </div>
                    </div>
                </div>
                ";
            }
            ?>
            
            <div class="row mb-3">
                <div class="offset-sm-3 col-sm-3 d-grid">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
                <div class="col-sm-3 d-grid">
                    <a class="btn btn-outline-secondary" href="enrollments.php" role="button">Cancel</a>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>