<?php
$connection = new mysqli("localhost", "root", "", "student_information_system");

if (!isset($_GET['dept_id'])) {
    header("Location: departments.php");
    exit;
}

$dept_id = $connection->real_escape_string($_GET['dept_id']);

// Get existing department data
$sql = "SELECT * FROM Departments WHERE dept_id = '$dept_id'";
$result = $connection->query($sql);
$department = $result->fetch_assoc();

if (!$department) {
    header("Location: departments.php");
    exit;
}

// Get department statistics
$stats_sql = "SELECT 
              (SELECT COUNT(*) FROM Programs WHERE dept_id = '$dept_id') as program_count,
              (SELECT COUNT(*) FROM Courses c JOIN Programs p ON c.program_id = p.program_id WHERE p.dept_id = '$dept_id') as course_count";
$stats_result = $connection->query($stats_sql);
$stats = $stats_result->fetch_assoc();

$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dept_name = $_POST["dept_name"];
    $description = $_POST["description"];
    
    do {
        if (empty($dept_name)) {
            $errorMessage = "Department Name is required";
            break;
        }
        
        $sql = "UPDATE Departments SET 
                dept_name = '$dept_name',
                description = " . (!empty($description) ? "'$description'" : "NULL") . "
                WHERE dept_id = '$dept_id'";
        
        $result = $connection->query($sql);
        
        if (!$result) {
            $errorMessage = "Invalid query: " . $connection->error;
            break;
        }
        
        $successMessage = "Department updated successfully";
        
        header("location: departments.php");
        exit;
    } while (false);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Department</title>
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
        .stat-item {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container my-5">
        <div class="form-container">
            <div class="form-header">
                <h2><i class="bi bi-pencil"></i> Edit Department</h2>
                <p class="text-muted">Update the department information below</p>
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
                        <label class="form-label">Department ID</label>
                        <input type="text" class="form-control" value="<?php echo $department['dept_id']; ?>" readonly>
                        <small class="text-muted">Department ID cannot be changed</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required-field">Department Name</label>
                        <input type="text" class="form-control" name="dept_name" 
                               value="<?php echo $department['dept_name']; ?>" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="4"><?php echo $department['description']; ?></textarea>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="bi bi-graph-up"></i> Department Statistics</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="stat-item">
                                    <h4 class="text-primary"><?php echo $stats['program_count']; ?></h4>
                                    <p class="text-muted mb-0">Programs</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-item">
                                    <h4 class="text-success"><?php echo $stats['course_count']; ?></h4>
                                    <p class="text-muted mb-0">Courses</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-item">
                                    <h4 class="text-info">12</h4>
                                    <p class="text-muted mb-0">Faculty</p>
                                </div>
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
                    <a href="departments.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Departments
                    </a>
                    <div>
                        <a href="view_department.php?dept_id=<?php echo $dept_id; ?>" class="btn btn-outline-primary me-2">
                            <i class="bi bi-eye"></i> View Department
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