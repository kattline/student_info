<?php
$connection = new mysqli("localhost", "root", "", "student_information_system");

$course_id = "";
$course_name = "";
$program_id = "";
$credit_hours = "";
$description = "";
$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_id = $_POST["course_id"];
    $course_name = $_POST["course_name"];
    $program_id = $_POST["program_id"];
    $credit_hours = $_POST["credit_hours"];
    $description = $_POST["description"];
    
    do {
        if (empty($course_id) || empty($course_name) || empty($program_id) || empty($credit_hours)) {
            $errorMessage = "Course ID, Name, Program, and Credit Hours are required";
            break;
        }
        
        // Check if course ID already exists
        $check_sql = "SELECT * FROM Courses WHERE course_id = '$course_id'";
        $check_result = $connection->query($check_sql);
        if ($check_result->num_rows > 0) {
            $errorMessage = "Course ID already exists";
            break;
        }
        
        $sql = "INSERT INTO Courses (course_id, course_name, program_id, credit_hours, description) 
                VALUES ('$course_id', '$course_name', '$program_id', '$credit_hours', " . 
                (!empty($description) ? "'$description'" : "NULL") . ")";
        
        $result = $connection->query($sql);
        
        if (!$result) {
            $errorMessage = "Invalid query: " . $connection->error;
            break;
        }
        
        $course_id = "";
        $course_name = "";
        $program_id = "";
        $credit_hours = "";
        $description = "";
        
        $successMessage = "Course added successfully";
        
        header("location: courses.php");
        exit;
    } while (false);
}

// Get programs for dropdown
$programs = $connection->query("SELECT program_id, program_name FROM Programs ORDER BY program_name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Course</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .form-header h2 {
            color: #0d6efd;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container my-5">
        <div class="form-container">
            <div class="form-header">
                <h2><i class="bi bi-book"></i> Add New Course</h2>
                <p class="text-muted">Fill in the details to create a new course</p>
            </div>
            
            <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error:</strong> <?php echo $errorMessage; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form method="post">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Course ID*</label>
                        <input type="text" class="form-control" name="course_id" value="<?php echo $course_id; ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Course Name*</label>
                        <input type="text" class="form-control" name="course_name" value="<?php echo $course_name; ?>" required>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Program*</label>
                        <select class="form-select" name="program_id" required>
                            <option value="">Select Program</option>
                            <?php while ($program = $programs->fetch_assoc()): ?>
                                <option value="<?php echo $program['program_id']; ?>" <?= $program_id == $program['program_id'] ? 'selected' : '' ?>>
                                    <?php echo $program['program_name']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Credit Hours*</label>
                        <input type="number" class="form-control" name="credit_hours" value="<?php echo $credit_hours; ?>" min="1" max="10" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Course Description</label>
                    <textarea class="form-control" name="description" rows="4"><?php echo $description; ?></textarea>
                </div>
                
                <?php if (!empty($successMessage)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Success!</strong> <?php echo $successMessage; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="courses.php" class="btn btn-outline-secondary me-md-2">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Save Course
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>