<?php
$connection = new mysqli("localhost", "root", "", "student_information_system");

if (!isset($_GET['student_id'])) {
    header("Location: students.php");
    exit;
}

$student_id = $_GET['student_id'];

// Get existing student data
$sql = "SELECT * FROM Students WHERE student_id = '$student_id'";
$result = $connection->query($sql);
$student = $result->fetch_assoc();

if (!$student) {
    header("Location: students.php");
    exit;
}

$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $date_of_birth = $_POST["date_of_birth"];
    $gender = $_POST["gender"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $address = $_POST["address"];
    
    do {
        if (empty($first_name) || empty($last_name)) {
            $errorMessage = "First name and last name are required";
            break;
        }
        
        $sql = "UPDATE Students SET 
                first_name = '$first_name',
                last_name = '$last_name',
                date_of_birth = " . (!empty($date_of_birth) ? "'$date_of_birth'" : "NULL") . ",
                gender = " . (!empty($gender) ? "'$gender'" : "NULL") . ",
                email = " . (!empty($email) ? "'$email'" : "NULL") . ",
                phone = " . (!empty($phone) ? "'$phone'" : "NULL") . ",
                address = " . (!empty($address) ? "'$address'" : "NULL") . "
                WHERE student_id = '$student_id'";
        
        $result = $connection->query($sql);
        
        if (!$result) {
            $errorMessage = "Invalid query: " . $connection->error;
            break;
        }
        
        $successMessage = "Student updated successfully";
        
        header("location: students.php");
        exit;
    } while (false);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .form-container {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-header {
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="form-container">
                    <div class="form-header">
                        <h2 class="text-primary"><i class="bi bi-pencil-square"></i> Edit Student</h2>
                        <p class="text-muted">Update student information below</p>
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
                                <label class="form-label">First Name*</label>
                                <input type="text" class="form-control" name="first_name" value="<?php echo $student['first_name']; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name*</label>
                                <input type="text" class="form-control" name="last_name" value="<?php echo $student['last_name']; ?>" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" name="date_of_birth" value="<?php echo $student['date_of_birth']; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Gender</label>
                                <select class="form-select" name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="Male" <?= $student['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                                    <option value="Female" <?= $student['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                                    <option value="Other" <?= $student['gender'] == 'Other' ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="<?php echo $student['email']; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="tel" class="form-control" name="phone" value="<?php echo $student['phone']; ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address" rows="3"><?php echo $student['address']; ?></textarea>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="students.php" class="btn btn-outline-secondary me-md-2">
                                <i class="bi bi-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>