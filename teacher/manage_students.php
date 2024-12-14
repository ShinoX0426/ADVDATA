<?php

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: index.php');
    exit();
}

// Database connection
$db = new mysqli('localhost', 'username', 'password', 'school_db');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$teacher_id = $_SESSION['user_id'];
$students = $db->query("SELECT * FROM students WHERE class_id IN (SELECT class_id FROM classes WHERE teacher_id = $teacher_id)");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <nav class="bg-blue-600 text-white p-4">
            <div class="container mx-auto flex justify-between items-center">
                <h1 class="text-2xl font-bold">Manage Students</h1>
                <a href="dashboard.php" class="bg-blue-500 hover:bg-blue-700 px-4 py-2 rounded">Back to Dashboard</a>
            </div>
        </nav>

        <main class="flex-grow container mx-auto mt-8 p-4">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-semibold mb-4">Your Students</h2>
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="p-2 text-left">ID</th>
                            <th class="p-2 text-left">Name</th>
                            <th class="p-2 text-left">Email</th>
                            <th class="p-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($student = $students->fetch_assoc()): ?>
                            <tr>
                                <td class="p-2"><?php echo $student['id']; ?></td>
                                <td class="p-2"><?php echo $student['name']; ?></td>
                                <td class="p-2"><?php echo $student['email']; ?></td>
                                <td class="p-2">
                                    <a href="edit_student.php?id=<?php echo $student['id']; ?>"
                                        class="text-blue-500 hover:underline">Edit</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>

        <footer class="bg-gray-200 text-center p-4 mt-8">
            <p>&copy; 2023 School Management System. All rights reserved.</p>
        </footer>
    </div>
</body>

</html>