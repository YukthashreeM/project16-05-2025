<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "workpausedb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $employee_id = $_POST["employee_id"];
    $basic_salary = $_POST["basic_salary"];
    $allowances = $_POST["allowances"];
    $deductions = $_POST["deductions"];

    $stmt = $conn->prepare("INSERT INTO payroll (employee_id, basic_salary, allowances, deductions) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sddd", $employee_id, $basic_salary, $allowances, $deductions);
    $stmt->execute();
    $stmt->close();
}

// Fetch payroll records
$result = $conn->query("SELECT * FROM payroll");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payroll Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f6ff;
            padding: 30px;
        }
        .back-btn {
            display: block;
            text-align: center;
            margin: 30px auto;
            padding: 12px 25px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            font-size: 1.1rem;
            border-radius: 8px;
            transition: background-color 0.3s ease;
            width: fit-content;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }

        h2 {
            text-align: center;
            color: #333;
        }
        form {
            width: 50%;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
        }
        form input[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }
        table {
            width: 90%;
            margin: 30px auto;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        .btn-payslip {
            padding: 5px 10px;
            background: #17a2b8;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .center {
            text-align: center;
        }
    </style>
</head>
<body>

<h2>Payroll Entry Form</h2>
<form method="post">
    <input type="text" name="employee_id" placeholder="Employee ID" required>
    <input type="number" name="basic_salary" placeholder="Basic Salary" required>
    <input type="number" name="allowances" placeholder="Allowances" required>
    <input type="number" name="deductions" placeholder="Deductions" required>
    <input type="submit" value="Submit">
</form>
<a href="admin.php" class="back-btn">← Back to Dashboard</a>

<h2>Payroll Records</h2>
<table>
    <tr>
        <th>Employee ID</th>
        <th>Basic Salary</th>
        <th>Allowances</th>
        <th>Deductions</th>
        <th>Net Salary</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): 
        $leave_q = $conn->query("SELECT COUNT(*) as leave_days FROM leaves WHERE employee_id='{$row["employee_id"]}' AND status='Approved'");
        $leave = $leave_q->fetch_assoc();
        $leave_deduction = ($row["basic_salary"] / 30) * ($leave["leave_days"] ?? 0);
        $total_deductions = $row["deductions"] + $leave_deduction;
        $net_salary = ($row["basic_salary"] + $row["allowances"]) - $total_deductions;
    ?>
    <tr>
        <td><?= $row["employee_id"] ?></td>
        <td><?= number_format($row["basic_salary"], 2) ?> INR</td>
        <td><?= number_format($row["allowances"], 2) ?> INR</td>
        <td><?= number_format($total_deductions, 2) ?> INR</td>
        <td><?= number_format($net_salary, 2) ?> INR</td>
        <td>
            <form method="post" action="payslip.php" target="_blank">
                <input type="hidden" name="employee_id" value="<?= $row["employee_id"] ?>">
                <input type="submit" value="View Payslip" class="btn-payslip">
            </form>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>