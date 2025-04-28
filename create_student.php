<?php
$connection = new mysqli("localhost", "root", "", "student_information_system");

$student_id = "";
$first_name = "";
$last_name = "";
$date_of_birth = "";
$gender = "";
$email = "";
$phone = "";
$address = "";
$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST["student_id"];
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $date_of_birth = $_POST["date_of_birth"];
    $gender = $_POST["gender"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $address = $_POST["address"];
    
    do {
        if (empty($student_id) || empty($first_name) || empty($last_name)) {
            $errorMessage = "Student ID, First Name, and Last Name are required";
            break;
        }
        
        // Check if student ID already exists
        $check_sql = "SELECT * FROM Students WHERE student_id = '$student_id'";
        $check_result = $connection->query($check_sql);
        if ($check_result->num_rows > 0) {
            $errorMessage = "Student ID already exists";
            break;
        }
        
        $sql = "INSERT INTO Students (student_id, first_name, last_name, date_of_birth, gender, email, phone, address) 
                VALUES ('$student_id', '$first_name', '$last_name', " . 
                (!empty($date_of_birth) ? "'$date_of_birth'" : "NULL") . ", " .
                (!empty($gender) ? "'$gender'" : "NULL") . ", " .
                (!empty($email) ? "'$email'" : "NULL") . ", " .
                (!empty($phone) ? "'$phone'" : "NULL") . ", " .
                (!empty($address) ? "'$address'" : "NULL") . ")";
        
        $result = $connection->query($sql);
        
        if (!$result) {
            $errorMessage = "Invalid query: " . $connection->error;
            break;
        }
        
        $student_id = "";
        $first_name = "";
        $last_name = "";
        $date_of_birth = "";
        $gender = "";
        $email = "";
        $phone = "";
        $address = "";
        
        $successMessage = "Student added successfully";
        
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
    <title>Add New Student</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container my-5">
        <h2><i class="bi bi-person-plus"></i> Add New Student</h2>
        
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
                <label class="col-sm-3 col-form-label">Student ID*</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="student_id" value="<?php echo $student_id; ?>" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">First Name*</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="first_name" value="<?php echo $first_name; ?>" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Last Name*</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="last_name" value="<?php echo $last_name; ?>" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Date of Birth</label>
                <div class="col-sm-6">
                    <input type="date" class="form-control" name="date_of_birth" value="<?php echo $date_of_birth; ?>">
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Gender</label>
                <div class="col-sm-6">
                    <select class="form-select" name="gender">
                        <option value="">Select Gender</option>
                        <option value="Male" <?php if($gender == "Male") echo "selected"; ?>>Male</option>
                        <option value="Female" <?php if($gender == "Female") echo "selected"; ?>>Female</option>
                        <option value="Other" <?php if($gender == "Other") echo "selected"; ?>>Other</option>
                    </select>
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Email</label>
                <div class="col-sm-6">
                    <input type="email" class="form-control" name="email" value="<?php echo $email; ?>">
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Phone</label>
                <div class="col-sm-6">
                    <input type="tel" class="form-control" name="phone" value="<?php echo $phone; ?>">
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Address</label>
                <div class="col-sm-6">
                    <textarea class="form-control" name="address"><?php echo $address; ?></textarea>
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
                    <a class="btn btn-outline-secondary" href="students.php" role="button">Cancel</a>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>