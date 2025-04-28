<?php
$connection = new mysqli("localhost", "root", "", "student_information_system");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-people"></i> Student Management</h2>
            <a href="create_student.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add New Student</a>
        </div>
        
        <!-- Search Form -->
        <form method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Search students..." 
                       value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <button class="btn btn-outline-secondary" type="submit">Search</button>
                <?php if (isset($_GET['search'])): ?>
                    <a href="students.php" class="btn btn-outline-danger">Clear</a>
                <?php endif; ?>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Admission Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $search = isset($_GET['search']) ? $connection->real_escape_string($_GET['search']) : "";
                    
                    $sql = "SELECT student_id, first_name, last_name, gender, email, phone, admission_date 
                            FROM Students";
                    
                    if (!empty($search)) {
                        $sql .= " WHERE student_id LIKE '%$search%' 
                                 OR first_name LIKE '%$search%' 
                                 OR last_name LIKE '%$search%'
                                 OR email LIKE '%$search%'
                                 OR phone LIKE '%$search%'";
                    }
                    
                    $sql .= " ORDER BY admission_date DESC";
                    
                    $result = $connection->query($sql);
                    
                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "
                            <tr>
                                <td>{$row['student_id']}</td>
                                <td>{$row['first_name']} {$row['last_name']}</td>
                                <td>{$row['gender']}</td>
                                <td>{$row['email']}</td>
                                <td>{$row['phone']}</td>
                                <td>{$row['admission_date']}</td>
                                <td>
                                    <div class='btn-group' role='group'>
                                        <a href='view_student.php?student_id={$row['student_id']}' class='btn btn-sm btn-info' title='View'>
                                            <i class='bi bi-eye'></i>
                                        </a>
                                        <a href='edit_student.php?student_id={$row['student_id']}' class='btn btn-sm btn-primary' title='Edit'>
                                            <i class='bi bi-pencil'></i>
                                        </a>
                                        <button class='btn btn-sm btn-danger' title='Delete' data-bs-toggle='modal' 
                                                data-bs-target='#deleteModal{$row['student_id']}'>
                                            <i class='bi bi-trash'></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Delete Confirmation Modal -->
                            <div class='modal fade' id='deleteModal{$row['student_id']}' tabindex='-1'>
                                <div class='modal-dialog'>
                                    <div class='modal-content'>
                                        <div class='modal-header bg-danger text-white'>
                                            <h5 class='modal-title'>Confirm Delete</h5>
                                            <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                        </div>
                                        <div class='modal-body'>
                                            Are you sure you want to delete student {$row['first_name']} {$row['last_name']} (ID: {$row['student_id']})?
                                            This action cannot be undone.
                                        </div>
                                        <div class='modal-footer'>
                                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                                            <a href='delete_student.php?student_id={$row['student_id']}' class='btn btn-danger'>Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            ";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center py-4'>No students found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>