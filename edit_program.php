<?php
$connection = new mysqli("localhost", "root", "", "student_information_system");

if (!isset($_GET['program_id'])) {
    header("Location: programs.php");
    exit;
}

$program_id = $connection->real_escape_string($_GET['program_id']);

// Get existing program data
$sql = "SELECT p.*, d.dept_name 
        FROM Programs p
        JOIN Departments d ON p.dept_id = d.dept_id
        WHERE p.program_id = '$program_id'";
$result = $connection->query($sql);
$program = $result->fetch_assoc();

if (!$program) {
    header("Location: programs.php");
    exit;
}

// Get program statistics
$stats_sql = "SELECT 
              (SELECT COUNT(*) FROM Courses WHERE program_id = '$program_id') as course_count,
              (SELECT COUNT(*) FROM Enrollments e JOIN Courses c ON e.course_id = c.course_id 
               WHERE c.program_id = '$program_id' AND e.status = 'Active') as active_students";
$stats_result = $connection->query($stats_sql);
$stats = $stats_result->fetch_assoc();

// Get departments for dropdown
$departments = $connection->query("SELECT dept_id, dept_name FROM Departments ORDER BY dept_name");

$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $program_name = $_POST["program_name"];
    $dept_id = $_POST["dept_id"];
    $duration_years = $_POST["duration_years"];
    $description = $_POST["description"];
    
    do {
        if (empty($program_name) || empty($dept_id) || empty($duration_years)) {
            $errorMessage = "Program Name, Department, and Duration are required";
            break;
        }
        
        $sql = "UPDATE Programs SET 
                program_name = '$program_name',
                dept_id = '$dept_id',
                duration_years = '$duration_years',
                description = " . (!empty($description) ? "'$description'" : "NULL") . "
                WHERE program_id = '$program_id'";
        
        $result = $connection->query($sql);
        
        if (!$result) {
            $errorMessage = "Invalid query: " . $connection->error;
            break;
        }
        
        $successMessage = "Program updated successfully";
        
        header("location: view_program.php?program_id=$program_id");
        exit;
    } while (false);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Program</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .form-container {
            max-width: 700px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .form-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #0d6efd;
        }
        .form-header h2 {
            color: #0d6efd;
        }
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
        .stat-card {
            border-left: 4px solid #0d6efd;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container my-5">
        <div class="form-container">
            <div class="form-header">
                <h2><i class="bi bi-pencil"></i> Edit Program</h2>
                <p class="text-muted">Update the program information below</p>
            </div>
            
            <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error:</strong> <?php echo $errorMessage; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form method="post">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Program ID</label>
                        <input type="text" class="form-control" value="<?php echo $program['program_id']; ?>" readonly>
                        <small class="text-muted">Program ID cannot be changed</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required-field">Program Name</label>
                        <input type="text" class="form-control" name="program_name" 
                               value="<?php echo $program['program_name']; ?>" required>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label required-field">Department</label>
                        <select class="form-select" name="dept_id" required>
                            <option value="">Select Department</option>
                            <?php 
                            $departments->data_seek(0); // Reset pointer
                            while ($dept = $departments->fetch_assoc()): ?>
                                <option value="<?php echo $dept['dept_id']; ?>" 
                                    <?= ($dept['dept_id'] == $program['dept_id']) ? 'selected' : '' ?>>
                                    <?php echo $dept['dept_name']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required-field">Duration (Years)</label>
                        <select class="form-select" name="duration_years" required>
                            <option value="">Select Duration</option>
                            <?php for ($i = 1; $i <= 6; $i++): ?>
                                <option value="<?php echo $i; ?>" <?= ($program['duration_years'] == $i) ? 'selected' : '' ?>>
                                    <?php echo $i; ?> year<?php echo $i > 1 ? 's' : ''; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Program Description</label>
                    <textarea class="form-control" name="description" rows="4"><?php echo $program['description']; ?></textarea>
                </div>
                
                <div class="card mb-4 stat-card">
                    <div class="card-body">
                        <h6 class="card-title"><i class="bi bi-graph-up"></i> Program Statistics</h6>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Courses:</strong> <?php echo $stats['course_count']; ?></p>
                                <p class="mb-1"><strong>Active Students:</strong> <?php echo $stats['active_students']; ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Faculty:</strong> 8</p>
                                <p class="mb-1"><strong>Graduates:</strong> 120</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($successMessage)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Success!</strong> <?php echo $successMessage; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="view_program.php?program_id=<?php echo $program_id; ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Program
                    </a>
                    <div>
                        <a href="view_department.php?dept_id=<?php echo $program['dept_id']; ?>" class="btn btn-outline-primary me-2">
                            <i class="bi bi-building"></i> View Department
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>