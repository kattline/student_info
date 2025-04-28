<?php
$connection = new mysqli("localhost", "root", "", "student_information_system");

$dept_id = isset($_GET['dept_id']) ? $_GET['dept_id'] : '';
$program_id = "";
$program_name = "";
$duration_years = "";
$description = "";
$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $program_id = $_POST["program_id"];
    $program_name = $_POST["program_name"];
    $dept_id = $_POST["dept_id"];
    $duration_years = $_POST["duration_years"];
    $description = $_POST["description"];
    
    do {
        if (empty($program_id) || empty($program_name) || empty($dept_id) || empty($duration_years)) {
            $errorMessage = "Program ID, Name, Department, and Duration are required";
            break;
        }
        
        // Check if program ID already exists
        $check_sql = "SELECT * FROM Programs WHERE program_id = '$program_id'";
        $check_result = $connection->query($check_sql);
        if ($check_result->num_rows > 0) {
            $errorMessage = "Program ID already exists";
            break;
        }
        
        $sql = "INSERT INTO Programs (program_id, program_name, dept_id, duration_years, description) 
                VALUES ('$program_id', '$program_name', '$dept_id', '$duration_years', " . 
                (!empty($description) ? "'$description'" : "NULL") . ")";
        
        $result = $connection->query($sql);
        
        if (!$result) {
            $errorMessage = "Invalid query: " . $connection->error;
            break;
        }
        
        $program_id = "";
        $program_name = "";
        $duration_years = "";
        $description = "";
        
        $successMessage = "Program created successfully";
        
        if (isset($_GET['dept_id'])) {
            header("location: view_department.php?dept_id=$dept_id");
        } else {
            header("location: programs.php");
        }
        exit;
    } while (false);
}

// Get departments for dropdown
$departments = $connection->query("SELECT dept_id, dept_name FROM Departments ORDER BY dept_name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Program</title>
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
                <h2><i class="bi bi-journal-plus"></i> Create New Program</h2>
                <p class="text-muted">Establish a new academic program within the system</p>
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
                        <label class="form-label required-field">Program ID</label>
                        <input type="text" class="form-control" name="program_id" value="<?php echo $program_id; ?>" 
                               placeholder="E.g., BSCS, BSIT" required>
                        <small class="text-muted">Unique identifier (usually 4-5 characters)</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required-field">Program Name</label>
                        <input type="text" class="form-control" name="program_name" value="<?php echo $program_name; ?>" 
                               placeholder="E.g., Bachelor of Science in Computer Science" required>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label required-field">Department</label>
                        <select class="form-select" name="dept_id" required>
                            <option value="">Select Department</option>
                            <?php while ($dept = $departments->fetch_assoc()): ?>
                                <option value="<?php echo $dept['dept_id']; ?>" 
                                    <?= ($dept['dept_id'] == $dept_id) ? 'selected' : '' ?>>
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
                                <option value="<?php echo $i; ?>" <?= ($duration_years == $i) ? 'selected' : '' ?>>
                                    <?php echo $i; ?> year<?php echo $i > 1 ? 's' : ''; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Program Description</label>
                    <textarea class="form-control" name="description" rows="4" 
                              placeholder="Brief description of the program"><?php echo $description; ?></textarea>
                </div>
                
                <div class="card mb-4 stat-card">
                    <div class="card-body">
                        <h6 class="card-title"><i class="bi bi-info-circle"></i> Program Statistics</h6>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Courses:</strong> 0</p>
                                <p class="mb-1"><strong>Active Students:</strong> 0</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Faculty:</strong> 0</p>
                                <p class="mb-1"><strong>Graduates:</strong> 0</p>
                            </div>
                        </div>
                        <small class="text-muted">Statistics will be available after program creation</small>
                    </div>
                </div>
                
                <?php if (!empty($successMessage)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Success!</strong> <?php echo $successMessage; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="d-flex justify-content-between mt-4">
                    <?php if (isset($_GET['dept_id'])): ?>
                        <a href="view_department.php?dept_id=<?php echo $dept_id; ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Department
                        </a>
                    <?php else: ?>
                        <a href="programs.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Programs
                        </a>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Create Program
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>