<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "workpausedb";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle appraisal entry
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_id = $_POST["employee_id"];
    $employee_name = $_POST["employee_name"];
    $appraisal_score = $_POST["appraisal_score"];

    // Calculate salary increment based on appraisal score
    if ($appraisal_score >= 90) {
        $salary_increment = 10000; // High performers get higher increment
    } elseif ($appraisal_score >= 75) {
        $salary_increment = 5000;
    } else {
        $salary_increment = 2000; // Low increment for lower scores
    }

    $sql = "INSERT INTO appraisal (employee_id, employee_name, appraisal_score, salary_increment) 
            VALUES ('$employee_id', '$employee_name', '$appraisal_score', '$salary_increment')";

    if ($conn->query($sql) === TRUE) {
        echo "Appraisal data added successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Employee Appraisal</title>
</head>
<body>
    <h2>Employee Appraisal Form</h2>
    <form method="post">
        <label>Employee ID:</label>
        <input type="text" name="employee_id" required><br><br>
        <label>Employee Name:</label>
        <input type="text" name="employee_name" required><br><br>
        <label>Appraisal Score (0-100):</label>
        <input type="number" name="appraisal_score" min="0" max="100" required><br><br>
        <input type="submit" value="Submit Appraisal">
    </form>
</body>
</html>