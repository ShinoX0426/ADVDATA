<?php
session_start();
require_once '../includes/database.php';
require_once '../includes/functions.php';

$database = new Database();
$db = $database->connect();

$user_data = check_login($db);

if ($user_data['role'] !== 'Teacher') {
    header("Location: ../index.php");
    die;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <nav class="bg-blue-600 text-white p-4">
            <div class="container mx-auto flex justify-between items-center">
                <h1 class="text-2xl font-bold">Teacher Dashboard</h1>
                <a href="logout.php" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded">Logout</a>
            </div>
        </nav>

        <main class="flex-grow container mx-auto mt-8 p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <a href="manage_students.php"
                    class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300">
                    <h2 class="text-xl font-semibold mb-2">View and Manage Students</h2>
                    <p class="text-gray-600">Access and update student information</p>
                </a>
                <a href="record_grades.php"
                    class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300">
                    <h2 class="text-xl font-semibold mb-2">Record Academic Grades</h2>
                    <p class="text-gray-600">Enter and update student grades</p>
                </a>
                <a href="report_violations.php"
                    class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300">
                    <h2 class="text-xl font-semibold mb-2">Report Rule Violations</h2>
                    <p class="text-gray-600">Submit reports for student misconduct</p>
                </a>
                <a href="view_attendance.php"
                    class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300">
                    <h2 class="text-xl font-semibold mb-2">View Student Attendance</h2>
                    <p class="text-gray-600">Check attendance records for your classes</p>
                </a>

            </div>
        </main>

        <footer class="bg-gray-200 text-center p-4 mt-8">
            <p>&copy; 2023 School Management System. All rights reserved.</p>
        </footer>
    </div>
</body>

</html>