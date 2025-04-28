<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="index.php">Student Information & Enrollment System</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php"><i class="bi bi-house"></i> Dashboard</a>
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
                <li class="nav-item">
                    <a class="nav-link" href="departments.php"><i class="bi bi-building"></i> Departments</a>
                </li>
            </ul>
            <form class="d-flex" method="GET" action="students.php">
                <input class="form-control me-2" type="search" name="search" placeholder="Search students...">
                <button class="btn btn-outline-light" type="submit">Search</button>
            </form>
        </div>
    </div>
</nav>