<?php
$connection = new mysqli("localhost", "root", "", "student_information_system");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-clipboard-check"></i> Enrollment Management</h2>
            <div>
                <a href="enroll_student.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> New Enrollment</a>
            </div>
        </div>
        
        <!-- Filter Form -->
        <form method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <select class="form-select" name="status">
                        <option value="">All Statuses</option>
                        <option value="Active" <?= isset($_GET['status']) && $_GET['status'] == 'Active' ? 'selected' : '' ?>>Active</option>
                        <option value="Completed" <?= isset($_GET['status']) && $_GET['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="Dropped" <?= isset($_GET['status']) && $_GET['status'] == 'Dropped' ? 'selected' : '' ?>>Dropped</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <input type="text" class="form-control" name="search" placeholder="Search by student or course..." 
                           value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100" type="submit">Filter</button>
                </div>
                <div class="col-md-2">
                    <a href="enrollments.php" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Enrollment ID</th>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Enrollment Date</th>
                        <th>Status</th>
                        <th>Grade</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $search = isset($_GET['search']) ? $connection->real_escape_string($_GET['search']) : "";
                    $status = isset($_GET['status']) ? $connection->real_escape_string($_GET['status']) : "";
                    
                    $sql = "SELECT e.enrollment_id, e.enrollment_date, e.status, e.grade,
                            s.student_id, CONCAT(s.first_name, ' ', s.last_name) as student_name,
                            c.course_id, c.course_name
                            FROM Enrollments e
                            JOIN Students s ON e.student_id = s.student_id
                            JOIN Courses c ON e.course_id = c.course_id";
                    
                    $where = [];
                    if (!empty($search)) {
                        $where[] = "(s.student_id LIKE '%$search%' OR 
                                    CONCAT(s.first_name, ' ', s.last_name) LIKE '%$search%' OR
                                    c.course_id LIKE '%$search%' OR
                                    c.course_name LIKE '%$search%')";
                    }
                    if (!empty($status)) {
                        $where[] = "e.status = '$status'";
                    }
                    
                    if (!empty($where)) {
                        $sql .= " WHERE " . implode(" AND ", $where);
                    }
                    
                    $sql .= " ORDER BY e.enrollment_date DESC";
                    
                    $result = $connection->query($sql);
                    
                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "
                            <tr>
                                <td>{$row['enrollment_id']}</td>
                                <td>{$row['student_name']} ({$row['student_id']})</td>
                                <td>{$row['course_name']} ({$row['course_id']})</td>
                                <td>" . date('m/d/Y', strtotime($row['enrollment_date'])) . "</td>
                                <td>
                                    <span class='badge " . 
                                    ($row['status'] == 'Active' ? 'bg-primary' : 
                                     ($row['status'] == 'Completed' ? 'bg-success' : 'bg-secondary')) . "'>
                                        {$row['status']}
                                    </span>
                                </td>
                                <td>" . (isset($row['grade']) ? $row['grade'] : 'N/A') . "</td>
                                <td>
                                    <div class='btn-group' role='group'>
                                        <a href='edit_enroll.php?enrollment_id={$row['enrollment_id']}' class='btn btn-sm btn-primary' title='Edit'>
                                            <i class='bi bi-pencil'></i>
                                        </a>
                                        <a href='unenroll.php?enrollment_id={$row['enrollment_id']}&redirect=enrollments' class='btn btn-sm btn-danger' title='Unenroll'>
                                            <i class='bi bi-trash'></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            ";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center py-4'>No enrollments found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>