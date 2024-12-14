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

$teacher_id = $_SESSION['user_id'];
$students = $db->query("SELECT * FROM students WHERE class_id IN (SELECT class_id FROM classes WHERE teacher_id = $teacher_id)");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $violation_type = $_POST['violation_type'];
    $description = $_POST['description'];
    $date = date('Y-m-d');

    $db->query("INSERT INTO violations (student_id, teacher_id, violation_type, description, date) 
                VALUES ($student_id, $teacher_id, '$violation_type', '$description', '$date')");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Rule Violations</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <nav class="bg-blue-600 text-white p-4">
            <div class="container mx-auto flex justify-between items-center">
                <h1 class="text-2xl font-bold">Report Rule Violations</h1>
                <a href="dashboard.php" class="bg-blue-500 hover:bg-blue-700 px-4 py-2 rounded">Back to Dashboard</a>
            </div>
        </nav>

        <main class="flex-grow container mx-auto mt-8 p-4">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-semibold mb-4">Submit Violation Report</h2>
                <form action="report_violations.php" method="POST" class="space-y-4">
                    <div>
                        <label for="student" class="block mb-1">Select Student:</label>
                        <select id="student" name="student_id" class="w-full p-2 border rounded" required>
                            <?php while ($student = $students->fetch_assoc()): ?>
                                <option value="<?php echo $student['id']; ?>"><?php echo $student['name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div>
                        <label for="violation_type" class="block mb-1">Violation Type:</label>
                        <input type="text" id="violation_type" name="violation_type" class="w-full p-2 border rounded"
                            required>
                    </div>
                    <div>
                        <label for="description" class="block mb-1">Description:</label>
                        <textarea id="description" name="description" class="w-full p-2 border rounded" rows="4"
                            required></textarea>
                    </div>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Submit
                        Report</button>
                </form>
            </div>
        </main>

        <footer class="bg-gray-200 text-center p-4 mt-8">
            <p>&copy; 2023 School Management System. All rights reserved.</p>
        </footer>
    </div>
</body>

</html>