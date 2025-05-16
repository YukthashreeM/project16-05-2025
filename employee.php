<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Employee') {
    header("Location: login.php");
    exit();
}

require 'db.php';

$username = htmlspecialchars($_SESSION['username']);
$role = htmlspecialchars($_SESSION['role'] ?? 'Employee');
$email = 'Not found';
$empId = 'N/A';

try {
    $stmt = $pdo->prepare("SELECT email, id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user) {
        $email = htmlspecialchars($user['email']);
        $empId = 'EMP' . str_pad($user['id'], 5, '0', STR_PAD_LEFT);
    }
} catch (PDOException $e) {
    $email = 'Error fetching email';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        .hidden-section {
            display: none;
        }
    </style>
</head>
<body class="bg-gray-50">

<!-- Main Wrapper -->
<div class="flex h-screen">

    <!-- Sidebar -->
    <div class="w-72 bg-gray-800 text-white p-6 flex flex-col space-y-8 border-r border-gray-700">
        <h2 class="text-2xl font-semibold">Dashboard</h2>
        
        <!-- Display Username, Email, and Emp ID -->
        <div class="mt-6 text-center">
            <p class="text-lg font-semibold"><?= $username ?></p>
            <p class="text-sm text-gray-400"><?= $email ?></p>
            <p class="text-sm text-gray-400"><?= $empId ?></p>
        </div>

        <div class="mt-6 space-y-6">
            <!-- Dashboard Sections -->
            <div>
                <button onclick="toggleSection('leave-section')" class="w-full text-left py-2 px-4 text-lg text-white hover:bg-indigo-600 rounded-lg flex items-center space-x-3">
                    <i class="fas fa-calendar-check"></i>
                    <span>Apply Leave</span>
                </button>
                <div id="leave-section" class="hidden-section pl-8 space-y-4 mt-4">
                    <a href="apply_leave.php" class="text-white">Submit leave request</a>
                    <a href="leave_history.php" class="text-white">View leave history</a>
                </div>
            </div>

            <div>
                <button onclick="toggleSection('cab-section')" class="w-full text-left py-2 px-4 text-lg text-white hover:bg-indigo-600 rounded-lg flex items-center space-x-3">
                    <i class="fas fa-taxi"></i>
                    <span>Cab Service</span>
                </button>
                <div id="cab-section" class="hidden-section pl-8 space-y-4 mt-4">
                    <a href="cab.php" class="text-white">Book office transportation</a>
                </div>
            </div>

            <div>
                <button onclick="toggleSection('settings-section')" class="w-full text-left py-2 px-4 text-lg text-white hover:bg-indigo-600 rounded-lg flex items-center space-x-3">
                    <i class="fas fa-cogs"></i>
                    <span>Account Settings</span>
                </button>
                <div id="settings-section" class="hidden-section pl-8 space-y-4 mt-4">
                    <a href="settings.php" class="text-white">Update your profile</a>
                </div>
            </div>

            <div>
                <button onclick="toggleSection('payslip-section')" class="w-full text-left py-2 px-4 text-lg text-white hover:bg-indigo-600 rounded-lg flex items-center space-x-3">
                    <i class="fas fa-receipt"></i>
                    <span>View Payslip</span>
                </button>
                <div id="payslip-section" class="hidden-section pl-8 space-y-4 mt-4">
                    <a href="payslip.php" class="text-white">View your salary slip</a>
                </div>
            </div>
             <div>
                <button onclick="toggleSection('payappraisal-section')" class="w-full text-left py-2 px-4 text-lg text-white hover:bg-indigo-600 rounded-lg flex items-center space-x-3">
                    <i class="fas fa-receipt"></i>
                    <span>View Payappraisal</span>
                </button>
                <div id="payappraisal-section" class="hidden-section pl-8 space-y-4 mt-4">
                    <a href="emp_appraisal.php" class="text-white">View your appraisal</a>
                </div>
            </div>

            <div>
                <button onclick="toggleSection('attendance-section')" class="w-full text-left py-2 px-4 text-lg text-white hover:bg-indigo-600 rounded-lg flex items-center space-x-3">
                    <i class="fas fa-clock"></i>
                    <span>Attendance</span>
                </button>
                <div id="attendance-section" class="hidden-section pl-8 space-y-4 mt-4">
                    <a href="attendance.php" class="text-white">Record your attendance</a>
                </div>
            </div>
        </div>

        <a href="logout.php" class="mt-auto bg-red-600 text-white py-2 px-6 rounded-lg text-center hover:bg-red-700 transition">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-10 bg-white overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-semibold text-gray-800">Welcome, <?= $username ?>!</h1>
        </div>

        <!-- Dashboard Info -->
        <div class="bg-white p-6 rounded-lg shadow-lg border border-gray-200 mb-6">
            <p class="text-lg text-gray-700">You can manage your leave, cab service, and more from here. Just click on any section to explore.</p>
        </div>

    </div>

</div>

<!-- JS to Toggle Sections -->
<script>
    function toggleSection(sectionId) {
        const section = document.getElementById(sectionId);
        if (section.style.display === "none" || section.style.display === "") {
            section.style.display = "block";
        } else {
            section.style.display = "none";
        }
    }
</script>

</body>
</html>