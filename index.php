<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Information & Enrollment System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .card-counter {
            box-shadow: 2px 2px 10px #DADADA;
            margin: 5px;
            padding: 20px 10px;
            border-radius: 5px;
            transition: .3s linear all;
        }
        .card-counter:hover {
            box-shadow: 4px 4px 20px #DADADA;
            transition: .3s linear all;
        }
        .card-counter.primary {
            background-color: #007bff;
            color: #FFF;
        }
        .card-counter.danger {
            background-color: #ef5350;
            color: #FFF;
        }  
        .card-counter.success {
            background-color: #66bb6a;
            color: #FFF;
        }  
        .card-counter.info {
            background-color: #26c6da;
            color: #FFF;
        }  
        .card-counter i {
            font-size: 5em;
            opacity: 0.2;
        }
        .card-counter .count-numbers {
            position: absolute;
            right: 35px;
            top: 20px;
            font-size: 32px;
            display: block;
        }
        .card-counter .count-name {
            position: absolute;
            right: 35px;
            top: 65px;
            font-style: italic;
            text-transform: capitalize;
            opacity: 0.5;
            display: block;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Student Information & Enrollment System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php"><i class="bi bi-house"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="students.php"><i class="bi bi-people"></i> Students</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="courses.php"><i class="bi bi-book"></i> Courses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="enrollments.php"><i class="bi bi-clipboard-check"></i> Enrollments</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h2 class="mb-4">Dashboard Overview</h2>
        
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card-counter primary">
                    <i class="bi bi-people"></i>
                    <span class="count-numbers">
                        <?php
                        $connection = new mysqli("localhost", "root", "", "student_information_system");
                        $result = $connection->query("SELECT COUNT(*) FROM Students");
                        echo $result->fetch_row()[0];
                        ?>
                    </span>
                    <span class="count-name">Students</span>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card-counter success">
                    <i class="bi bi-book"></i>
                    <span class="count-numbers">
                        <?php
                        $result = $connection->query("SELECT COUNT(*) FROM Courses");
                        echo $result->fetch_row()[0];
                        ?>
                    </span>
                    <span class="count-name">Courses</span>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card-counter info">
                    <i class="bi bi-clipboard-check"></i>
                    <span class="count-numbers">
                        <?php
                        $result = $connection->query("SELECT COUNT(*) FROM Enrollments WHERE status = 'Active'");
                        echo $result->fetch_row()[0];
                        ?>
                    </span>
                    <span class="count-name">Active Enrollments</span>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card-counter danger">
                    <i class="bi bi-building"></i>
                    <span class="count-numbers">
                        <?php
                        $result = $connection->query("SELECT COUNT(*) FROM Departments");
                        echo $result->fetch_row()[0];
                        ?>
                    </span>
                    <span class="count-name">Departments</span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0"><i class="bi bi-people"></i> Recent Students</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Admission Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result = $connection->query("SELECT student_id, CONCAT(first_name, ' ', last_name) as name, admission_date 
                                                             FROM Students ORDER BY admission_date DESC LIMIT 5");
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                            <td>{$row['student_id']}</td>
                                            <td>{$row['name']}</td>
                                            <td>{$row['admission_date']}</td>
                                          </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                        <a href="students.php" class="btn btn-primary btn-sm">View All Students</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0"><i class="bi bi-book"></i> Recent Enrollments</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Course</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result = $connection->query("SELECT s.student_id, CONCAT(s.first_name, ' ', s.last_name) as student_name, 
                                                             c.course_name, e.enrollment_date
                                                             FROM Enrollments e
                                                             JOIN Students s ON e.student_id = s.student_id
                                                             JOIN Courses c ON e.course_id = c.course_id
                                                             ORDER BY e.enrollment_date DESC LIMIT 5");
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                            <td>{$row['student_name']}</td>
                                            <td>{$row['course_name']}</td>
                                            <td>{$row['enrollment_date']}</td>
                                          </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                        <a href="enrollments.php" class="btn btn-success btn-sm">View All Enrollments</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>