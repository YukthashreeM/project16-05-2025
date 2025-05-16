<?php
include 'config.php';
session_start();


    $dashboard = match ($_SESSION['role']) {
        'Employee' => 'employee.php',
        'Manager' => 'manager.php',
        'Admin' => 'admin.php',
        default => 'login.php'
    };


// Allow all logged-in users (Employee, Manager, Admin)
if (!isset($_SESSION['id']) || !isset($_SESSION['role'])) {
    echo "<p style='color:red; text-align:center;'>⚠️ You must be logged in to view this page.</p>";
    exit;
}

$emp_id = $_SESSION['id'];  // All users see their own payslip

// Fetch employee name
$name_stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$name_stmt->bind_param("i", $emp_id);
$name_stmt->execute();
$name_res = $name_stmt->get_result();
$name_row = $name_res->fetch_assoc();
$emp_name = $name_row['username'] ?? 'Unknown';

// Fetch latest payroll data
$stmt = $conn->prepare("SELECT * FROM payroll WHERE employee_id = ? ORDER BY id DESC LIMIT 1");
$stmt->bind_param("i", $emp_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo "<p style='color:red; text-align:center;'>❌ No payroll record found for your account.</p>";
    exit;
}

$row = $res->fetch_assoc();

// Get approved leave count
$leave_stmt = $conn->prepare("SELECT COUNT(*) as leave_days FROM leaves WHERE employee_id = ? AND status = 'Approved'");
$leave_stmt->bind_param("i", $emp_id);
$leave_stmt->execute();
$leave_res = $leave_stmt->get_result();
$leave = $leave_res->fetch_assoc();

$leave_days = $leave['leave_days'] ?? 0;
$daily_wage = $row["basic_salary"] / 30;
$leave_deduction = $daily_wage * $leave_days;
$total_deductions = $row["deductions"] + $leave_deduction;
$net_salary = ($row["basic_salary"] + $row["allowances"]) - $total_deductions;
?>

<!-- The rest of your HTML remains unchanged -->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payslip</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f8;
            padding: 40px;
        }
        .slip {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h2, h3 {
            text-align: center;
            color: #2c3e50;
        }
        table {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
        }
        td {
            padding: 12px 8px;
            border-bottom: 1px solid #e1e1e1;
        }
        td:first-child {
            font-weight: 600;
            color: #34495e;
        }
        .print-btn, .back-btn {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 24px;
            border: none;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
        }
        .print-btn {
            background-color: #2ecc71;
            color: white;
            margin-right: 15px;
        }
        .print-btn:hover {
            background-color: #27ae60;
        }
        .back-btn {
            background-color: #3498db;
            color: white;
        }
        .back-btn:hover {
            background-color: #2980b9;
        }
        .center {
            text-align: center;
        }
    </style>
</head>
<body>
<div class="slip">
    <h2>Payslip</h2>
    <h3>Name: <?= htmlspecialchars($emp_name) ?></h3>
    <h3>Employee ID: <?= htmlspecialchars($emp_id) ?></h3>
    <h3>Role: <?= htmlspecialchars($_SESSION['role']) ?></h3>


    <table>
        <tr><td>Basic Salary</td><td>₹<?= number_format($row["basic_salary"], 2) ?></td></tr>
        <tr><td>Allowances</td><td>₹<?= number_format($row["allowances"], 2) ?></td></tr>
        <tr><td>Leave Deduction (<?= $leave_days ?> day<?= $leave_days != 1 ? 's' : '' ?>)</td><td>₹<?= number_format($leave_deduction, 2) ?></td></tr>
        <tr><td>Other Deductions</td><td>₹<?= number_format($row["deductions"], 2) ?></td></tr>
        <tr><td><strong>Net Salary</strong></td><td><strong>₹<?= number_format($net_salary, 2) ?></strong></td></tr>
    </table>

    <div class="center">
        <button onclick="window.print()" class="print-btn">🖨 Print Payslip</button>
        <a href="<?= $dashboard ?>" class="back-btn">← Back to Dashboard</a>
    </div>
</div>
</body>
</html>
