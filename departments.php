<?php
$connection = new mysqli("localhost", "root", "", "student_information_system");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-building"></i> Department Management</h2>
            <a href="create_department.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add Department</a>
        </div>
        
        <!-- Search Form -->
        <form method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Search departments..." 
                       value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <button class="btn btn-outline-secondary" type="submit">Search</button>
                <?php if (isset($_GET['search'])): ?>
                    <a href="departments.php" class="btn btn-outline-danger">Clear</a>
                <?php endif; ?>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Department ID</th>
                        <th>Department Name</th>
                        <th>Programs</th>
                        <th>Courses</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $search = isset($_GET['search']) ? $connection->real_escape_string($_GET['search']) : "";
                    
                    $sql = "SELECT d.dept_id, d.dept_name, d.description,
                            COUNT(DISTINCT p.program_id) as program_count,
                            COUNT(DISTINCT c.course_id) as course_count
                            FROM Departments d
                            LEFT JOIN Programs p ON d.dept_id = p.dept_id
                            LEFT JOIN Courses c ON p.program_id = c.program_id";
                    
                    if (!empty($search)) {
                        $sql .= " WHERE d.dept_id LIKE '%$search%' 
                                 OR d.dept_name LIKE '%$search%'
                                 OR d.description LIKE '%$search%'";
                    }
                    
                    $sql .= " GROUP BY d.dept_id
                              ORDER BY d.dept_id";
                    
                    $result = $connection->query($sql);
                    
                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "
                            <tr>
                                <td>{$row['dept_id']}</td>
                                <td>{$row['dept_name']}</td>
                                <td>{$row['program_count']}</td>
                                <td>{$row['course_count']}</td>
                                <td>
                                    <div class='btn-group' role='group'>
                                        <a href='view_department.php?dept_id={$row['dept_id']}' class='btn btn-sm btn-info' title='View'>
                                            <i class='bi bi-eye'></i>
                                        </a>
                                        <a href='edit_department.php?dept_id={$row['dept_id']}' class='btn btn-sm btn-primary' title='Edit'>
                                            <i class='bi bi-pencil'></i>
                                        </a>
                                        <button class='btn btn-sm btn-danger' title='Delete' data-bs-toggle='modal' 
                                                data-bs-target='#deleteModal{$row['dept_id']}'>
                                            <i class='bi bi-trash'></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Delete Confirmation Modal -->
                            <div class='modal fade' id='deleteModal{$row['dept_id']}' tabindex='-1'>
                                <div class='modal-dialog'>
                                    <div class='modal-content'>
                                        <div class='modal-header bg-danger text-white'>
                                            <h5 class='modal-title'>Confirm Delete</h5>
                                            <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                        </div>
                                        <div class='modal-body'>
                                            Are you sure you want to delete department {$row['dept_id']} - {$row['dept_name']}?
                                            This will also delete all associated programs and courses.
                                        </div>
                                        <div class='modal-footer'>
                                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                                            <a href='delete_department.php?dept_id={$row['dept_id']}' class='btn btn-danger'>Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            ";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center py-4'>No departments found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>