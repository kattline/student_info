<?php
$connection = new mysqli("localhost", "root", "", "student_information_system");

if (isset($_GET["enrollment_id"]) && isset($_GET["student_id"])) {
    $enrollment_id = $connection->real_escape_string($_GET["enrollment_id"]);
    $student_id = $connection->real_escape_string($_GET["student_id"]);
    
    // Instead of deleting, mark as dropped to preserve history
    $sql = "UPDATE Enrollments SET status = 'Dropped' WHERE enrollment_id = '$enrollment_id'";
    $connection->query($sql);
}

header("Location: view_student.php?student_id=" . $_GET["student_id"]);
exit;
?>