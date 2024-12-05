<?php
session_start();
require_once '../includes/database.php';
require_once '../includes/user.class.php';
require_once '../includes/functions.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

$database = new Database();
$db = $database->connect();
$user = new User($db);

$admin_name = $_SESSION['username'];

// Fetch some statistics for the dashboard
$total_users = $user->getTotalUsers();
$total_students = $user->getTotalUsersByRole('Student');
$total_teachers = $user->getTotalUsersByRole('Teacher');
$total_counselors = $user->getTotalUsersByRole('Counselor');

$csrf_token = generate_csrf_token();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - DPLMHS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <!-- Navigation -->
        <nav class="bg-blue-600 text-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center">
                        <i class="fas fa-school text-2xl mr-2"></i>
                        <span class="font-bold text-xl">DPLMHS Admin</span>
                    </div>
                    <div class="flex items-center">
                        <span class="mr-4">Welcome, <?php echo htmlspecialchars($admin_name); ?></span>
                        <a href="../logout.php?csrf_token=<?php echo $csrf_token; ?>"
                            class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded transition duration-300">
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-grow container mx-auto px-4 py-8">
            <h1 class="text-3xl font-bold mb-8">Admin Dashboard</h1>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-2">Total Users</h2>
                    <p class="text-3xl font-bold text-blue-600"><?php echo $total_users; ?></p>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-2">Total Students</h2>
                    <p class="text-3xl font-bold text-green-600"><?php echo $total_students; ?></p>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-2">Total Teachers</h2>
                    <p class="text-3xl font-bold text-yellow-600"><?php echo $total_teachers; ?></p>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-2">Total Counselors</h2>
                    <p class="text-3xl font-bold text-purple-600"><?php echo $total_counselors; ?></p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-2xl font-bold mb-4">Quick Actions</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <a href="manage_users.php"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition duration-300 text-center">
                        Manage Users
                    </a>
                    <a href="manage_classes.php"
                        class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded transition duration-300 text-center">
                        Manage Classes
                    </a>
                    <a href="manage_subjects.php"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded transition duration-300 text-center">
                        Manage Subjects
                    </a>
                    <a href="view_reports.php"
                        class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded transition duration-300 text-center">
                        View Reports
                    </a>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold mb-4">Recent Activities</h2>
                <ul class="divide-y divide-gray-200">
                    <li class="py-3">
                        <p class="text-gray-600">New user registered: John Doe (Student)</p>
                        <p class="text-sm text-gray-500">2 hours ago</p>
                    </li>
                    <li class="py-3">
                        <p class="text-gray-600">Class schedule updated: Grade 10 - Section A</p>
                        <p class="text-sm text-gray-500">4 hours ago</p>
                    </li>
                    <li class="py-3">
                        <p class="text-gray-600">New violation report submitted by Teacher Jane Smith</p>
                        <p class="text-sm text-gray-500">Yesterday at 3:45 PM</p>
                    </li>
                </ul>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-200 text-center py-4">
            <p>&copy; 2023 Don Pablo Lorenzo Memorial High School. All rights reserved.</p>
        </footer>
    </div>
</body>

</html>