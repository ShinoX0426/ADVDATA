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
$classes = $db->query("SELECT * FROM classes WHERE teacher_id = $teacher_id");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $class_id = $_POST['class_id'];
    $grade = $_POST['grade'];

    $db->query("INSERT INTO grades (student_id, class_id, grade) VALUES ($student_id, $class_id, '$grade') 
                ON DUPLICATE KEY UPDATE grade = '$grade'");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Academic Grades</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <nav class="bg-blue-600 text-white p-4">
            <div class="container mx-auto flex justify-between items-center">
                <h1 class="text-2xl font-bold">Record Academic Grades</h1>
                <a href="dashboard.php" class="bg-blue-500 hover:bg-blue-700 px-4 py-2 rounded">Back to Dashboard</a>
            </div>
        </nav>

        <main class="flex-grow container mx-auto mt-8 p-4">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-semibold mb-4">Enter Grades</h2>
                <form id="gradeForm" class="space-y-4">
                    <div>
                        <label for="class" class="block mb-1">Select Class:</label>
                        <select id="class" name="class_id" class="w-full p-2 border rounded">
                            <?php while ($class = $classes->fetch_assoc()): ?>
                                <option value="<?php echo $class['id']; ?>"><?php echo $class['name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div id="studentList"></div>
                </form>
            </div>
        </main>

        <footer class="bg-gray-200 text-center p-4 mt-8">
            <p>&copy; 2023 School Management System. All rights reserved.</p>
        </footer>
    </div>

    <script>
        $(document).ready(function () {
            $('#class').change(function () {
                var classId = $(this).val();
                $.ajax({
                    url: 'get_students.php',
                    method: 'POST',
                    data: { class_id: classId },
                    success: function (response) {
                        $('#studentList').html(response);
                    }
                });
            });

            $(document).on('submit', '#gradeForm', function (e) {
                e.preventDefault();
                $.ajax({
                    url: 'record_grades.php',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        alert('Grades recorded successfully');
                    }
                });
            });
        });
    </script>
</body>

</html>