<?php
$connection = new mysqli("localhost", "root", "", "student_information_system");

if (!isset($_GET['course_id'])) {
    header("Location: courses.php");
    exit;
}

$course_id = $connection->real_escape_string($_GET['course_id']);

// Get existing course data
$sql = "SELECT * FROM Courses WHERE course_id = '$course_id'";
$result = $connection->query($sql);
$course = $result->fetch_assoc();

if (!$course) {
    header("Location: courses.php");
    exit;
}

$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_name = $_POST["course_name"];
    $program_id = $_POST["program_id"];
    $credit_hours = $_POST["credit_hours"];
    $description = $_POST["description"];
    
    do {
        if (empty($course_name) || empty($program_id) || empty($credit_hours)) {
            $errorMessage = "Course Name, Program, and Credit Hours are required";
            break;
        }
        
        $sql = "UPDATE Courses SET 
                course_name = '$course_name',
                program_id = '$program_id',
                credit_hours = '$credit_hours',
                description = " . (!empty($description) ? "'$description'" : "NULL") . "
                WHERE course_id = '$course_id'";
        
        $result = $connection->query($sql);
        
        if (!$result) {
            $errorMessage = "Invalid query: " . $connection->error;
            break;
        }
        
        $successMessage = "Course updated successfully";
        
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
    <title>Edit Course</title>
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
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 15px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container my-5">
        <div class="form-container">
            <div class="form-header">
                <h2><i class="bi bi-pencil"></i> Edit Course</h2>
                <p class="text-muted">Update the course information below</p>
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
                        <label class="form-label">Course ID</label>
                        <input type="text" class="form-control" value="<?php echo $course['course_id']; ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Course Name*</label>
                        <input type="text" class="form-control" name="course_name" value="<?php echo $course['course_name']; ?>" required>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Program*</label>
                        <select class="form-select" name="program_id" required>
                            <option value="">Select Program</option>
                            <?php 
                            $programs->data_seek(0); // Reset pointer
                            while ($program = $programs->fetch_assoc()): ?>
                                <option value="<?php echo $program['program_id']; ?>" <?= $course['program_id'] == $program['program_id'] ? 'selected' : '' ?>>
                                    <?php echo $program['program_name']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Credit Hours*</label>
                        <input type="number" class="form-control" name="credit_hours" value="<?php echo $course['credit_hours']; ?>" min="1" max="10" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Course Description</label>
                    <textarea class="form-control" name="description" rows="4"><?php echo $course['description']; ?></textarea>
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
                        <i class="bi bi-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>