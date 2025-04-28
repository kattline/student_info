<?php
$connection = new mysqli("localhost", "root", "", "student_information_system");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $connection->real_escape_string($_POST["student_id"]);
    $course_id = $connection->real_escape_string($_POST["course_id"]);
    
    // Check if already enrolled (even if dropped)
    $check_sql = "SELECT * FROM Enrollments WHERE student_id = '$student_id' AND course_id = '$course_id'";
    $check_result = $connection->query($check_sql);
    
    if ($check_result->num_rows > 0) {
        // Update status to Active if previously dropped
        $update_sql = "UPDATE Enrollments SET status = 'Active' 
                       WHERE student_id = '$student_id' AND course_id = '$course_id'";
        $connection->query($update_sql);
    } else {
        // New enrollment
        $insert_sql = "INSERT INTO Enrollments (student_id, course_id) VALUES ('$student_id', '$course_id')";
        $connection->query($insert_sql);
    }
}

header("Location: view_student.php?student_id=" . $_POST["student_id"]);
exit;
?>