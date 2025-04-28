<?php
$connection = new mysqli("localhost", "root", "", "student_information_system");

$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST["student_id"];
    $course_id = $_POST["course_id"];
    
    do {
        if (empty($student_id) || empty($course_id)) {
            $errorMessage = "Both student and course are required";
            break;
        }
        
        // Check if student exists
        $student_check = $connection->query("SELECT * FROM Students WHERE student_id = '$student_id'");
        if ($student_check->num_rows == 0) {
            $errorMessage = "Student not found";
            break;
        }
        
        // Check if course exists
        $course_check = $connection->query("SELECT * FROM Courses WHERE course_id = '$course_id'");
        if ($course_check->num_rows == 0) {
            $errorMessage = "Course not found";
            break;
        }
        
        // Check if already enrolled (even if dropped)
        $enrollment_check = $connection->query("SELECT * FROM Enrollments 
                                              WHERE student_id = '$student_id' AND course_id = '$course_id'");
        
        if ($enrollment_check->num_rows > 0) {
            // Update status to Active if previously dropped
            $update_sql = "UPDATE Enrollments SET status = 'Active' 
                           WHERE student_id = '$student_id' AND course_id = '$course_id'";
            $result = $connection->query($update_sql);
        } else {
            // New enrollment
            $insert_sql = "INSERT INTO Enrollments (student_id, course_id) VALUES ('$student_id', '$course_id')";
            $result = $connection->query($insert_sql);
        }
        
        if (!$result) {
            $errorMessage = "Invalid query: " . $connection->error;
            break;
        }
        
        $successMessage = "Enrollment created successfully";
        
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
    <title>New Enrollment</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container my-5">
        <h2><i class="bi bi-clipboard-plus"></i> New Enrollment</h2>
        
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
                <label class="col-sm-3 col-form-label">Student*</label>
                <div class="col-sm-6">
                    <select class="form-select" name="student_id" required>
                        <option value="">Select Student</option>
                        <?php
                        $students = $connection->query("SELECT student_id, CONCAT(first_name, ' ', last_name) as name FROM Students ORDER BY name");
                        while ($student = $students->fetch_assoc()) {
                            $selected = isset($_POST['student_id']) && $_POST['student_id'] == $student['student_id'] ? 'selected' : '';
                            echo "<option value='{$student['student_id']}' $selected>{$student['name']} ({$student['student_id']})</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Course*</label>
                <div class="col-sm-6">
                    <select class="form-select" name="course_id" required>
                        <option value="">Select Course</option>
                        <?php
                        $courses = $connection->query("SELECT c.course_id, c.course_name, p.program_name 
                                                      FROM Courses c
                                                      JOIN Programs p ON c.program_id = p.program_id
                                                      ORDER BY c.course_name");
                        while ($course = $courses->fetch_assoc()) {
                            $selected = isset($_POST['course_id']) && $_POST['course_id'] == $course['course_id'] ? 'selected' : '';
                            echo "<option value='{$course['course_id']}' $selected>{$course['course_name']} ({$course['program_name']})</option>";
                        }
                        ?>
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
                    <button type="submit" class="btn btn-primary">Submit</button>
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