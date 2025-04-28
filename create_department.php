<?php
$connection = new mysqli("localhost", "root", "", "student_information_system");

$dept_id = "";
$dept_name = "";
$description = "";
$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dept_id = $_POST["dept_id"];
    $dept_name = $_POST["dept_name"];
    $description = $_POST["description"];
    
    do {
        if (empty($dept_id) || empty($dept_name)) {
            $errorMessage = "Department ID and Name are required";
            break;
        }
        
        // Check if department ID already exists
        $check_sql = "SELECT * FROM Departments WHERE dept_id = '$dept_id'";
        $check_result = $connection->query($check_sql);
        if ($check_result->num_rows > 0) {
            $errorMessage = "Department ID already exists";
            break;
        }
        
        $sql = "INSERT INTO Departments (dept_id, dept_name, description) 
                VALUES ('$dept_id', '$dept_name', " . 
                (!empty($description) ? "'$description'" : "NULL") . ")";
        
        $result = $connection->query($sql);
        
        if (!$result) {
            $errorMessage = "Invalid query: " . $connection->error;
            break;
        }
        
        $dept_id = "";
        $dept_name = "";
        $description = "";
        
        $successMessage = "Department created successfully";
        
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
    <title>Create New Department</title>
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
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container my-5">
        <div class="form-container">
            <div class="form-header">
                <h2><i class="bi bi-building-add"></i> Create New Department</h2>
                <p class="text-muted">Fill in the details to establish a new academic department</p>
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
                        <label class="form-label required-field">Department ID</label>
                        <input type="text" class="form-control" name="dept_id" value="<?php echo $dept_id; ?>" 
                               placeholder="E.g., CS, MATH, PHY" required>
                        <small class="text-muted">Unique identifier (2-5 characters)</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required-field">Department Name</label>
                        <input type="text" class="form-control" name="dept_name" value="<?php echo $dept_name; ?>" 
                               placeholder="E.g., Computer Science" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="4" 
                              placeholder="Brief description of the department"><?php echo $description; ?></textarea>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="bi bi-info-circle"></i> Department Statistics (Will be available after creation)</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="stat-item">
                                    <h4 class="text-primary">0</h4>
                                    <p class="text-muted mb-0">Programs</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-item">
                                    <h4 class="text-success">0</h4>
                                    <p class="text-muted mb-0">Courses</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-item">
                                    <h4 class="text-info">0</h4>
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
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Create Department
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>