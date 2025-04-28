<?php
$connection = new mysqli("localhost", "root", "", "student_information_system");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-book"></i> Course Management</h2>
            <a href="create_course.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add New Course</a>
        </div>
        
        <!-- Search Form -->
        <form method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Search courses..." 
                       value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <button class="btn btn-outline-secondary" type="submit">Search</button>
                <?php if (isset($_GET['search'])): ?>
                    <a href="courses.php" class="btn btn-outline-danger">Clear</a>
                <?php endif; ?>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Course ID</th>
                        <th>Course Name</th>
                        <th>Program</th>
                        <th>Credit Hours</th>
                        <th>Enrolled Students</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $search = isset($_GET['search']) ? $connection->real_escape_string($_GET['search']) : "";
                    
                    $sql = "SELECT c.course_id, c.course_name, p.program_name, c.credit_hours, 
                            COUNT(e.student_id) as enrolled_students
                            FROM Courses c
                            LEFT JOIN Programs p ON c.program_id = p.program_id
                            LEFT JOIN Enrollments e ON c.course_id = e.course_id AND e.status = 'Active'";
                    
                    if (!empty($search)) {
                        $sql .= " WHERE c.course_id LIKE '%$search%' 
                                 OR c.course_name LIKE '%$search%'
                                 OR p.program_name LIKE '%$search%'";
                    }
                    
                    $sql .= " GROUP BY c.course_id
                              ORDER BY c.course_id";
                    
                    $result = $connection->query($sql);
                    
                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "
                            <tr>
                                <td>{$row['course_id']}</td>
                                <td>{$row['course_name']}</td>
                                <td>{$row['program_name']}</td>
                                <td>{$row['credit_hours']}</td>
                                <td>{$row['enrolled_students']}</td>
                                <td>
                                    <div class='btn-group' role='group'>
                                        <a href='view_course.php?course_id={$row['course_id']}' class='btn btn-sm btn-info' title='View'>
                                            <i class='bi bi-eye'></i>
                                        </a>
                                        <a href='edit_course.php?course_id={$row['course_id']}' class='btn btn-sm btn-primary' title='Edit'>
                                            <i class='bi bi-pencil'></i>
                                        </a>
                                        <button class='btn btn-sm btn-danger' title='Delete' data-bs-toggle='modal' 
                                                data-bs-target='#deleteModal{$row['course_id']}'>
                                            <i class='bi bi-trash'></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Delete Confirmation Modal -->
                            <div class='modal fade' id='deleteModal{$row['course_id']}' tabindex='-1'>
                                <div class='modal-dialog'>
                                    <div class='modal-content'>
                                        <div class='modal-header bg-danger text-white'>
                                            <h5 class='modal-title'>Confirm Delete</h5>
                                            <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                        </div>
                                        <div class='modal-body'>
                                            Are you sure you want to delete course {$row['course_id']} - {$row['course_name']}?
                                            This will also delete all enrollment records for this course.
                                        </div>
                                        <div class='modal-footer'>
                                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                                            <a href='delete_course.php?course_id={$row['course_id']}' class='btn btn-danger'>Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            ";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center py-4'>No courses found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>