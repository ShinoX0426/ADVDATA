<?php
// view_attendance.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit();
}

// Database connection
$db = new mysqli('localhost', 'username', 'password', 'school_db');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$teacher_id = $_SESSION['user_id'];
$classes = $db->query("SELECT * FROM classes WHERE teacher_id = $teacher_id");

$selected_class = isset($_GET['class_id']) ? $_GET['class_id'] : $_GET['class_id'] = null;

if ($selected_class) {
    $students = $db->query("SELECT s.*, a.date, a.status 
                            FROM students s
                            LEFT JOIN attendance a ON s.id = a.student_id AND a.class_id = $selected_class
                            WHERE s.class_id = $selected_class
                            ORDER BY s.name, a.date");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student Attendance</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <nav class="bg-blue-600 text-white p-4">
            <div class="container mx-auto flex justify-between items-center">
                <h1 class="text-2xl font-bold">View Student Attendance</h1>
                <a href="dashboard.php" class="bg-blue-500 hover:bg-blue-700 px-4 py-2 rounded">Back to Dashboard</a>
            </div>
        </nav>

        <main class="flex-grow container mx-auto mt-8 p-4">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-semibold mb-4">Select Class</h2>
                <form action="view_attendance.php" method="GET" class="mb-4">
                    <select name="class_id" class="w-full p-2 border rounded" onchange="this.form.submit()">
                        <option value="">Select a class</option>
                        <?php while ($class = $classes->fetch_assoc()): ?>
                            <option value="<?php echo $class['id']; ?>" <?php echo $selected_class == $class['id'] ? 'selected' : ''; ?>>
                                <?php echo $class['name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </form>

                <?php if ($selected_class && $students->num_rows > 0): ?>
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="p-2 text-left">Student Name</th>
                                <th class="p-2 text-left">Date</th>
                                <th class="p-2 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($student = $students->fetch_assoc()): ?>
                                <tr>
                                    <td class="p-2 border-t"><?php echo $student['name']; ?></td>
                                    <td class="p-2 border-t"><?php echo $student['date'] ? $student['date'] : 'N/A'; ?></td>
                                    <td class="p-2 border-t"><?php echo $student['status'] ? $student['status'] : 'N/A'; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php elseif ($selected_class): ?>
                    <p class="text-center">No attendance records found for this class.</p>
                <?php endif; ?>
            </div>
        </main>

        <footer class="bg-gray-200 text-center p-4 mt-8">
            <p>&copy; 2023 School Management System. All rights reserved.</p>
        </footer>
    </div>
</body>

</html>