<?php
session_start();
require_once '../includes/database.php';
require_once '../includes/functions.php';

$database = new Database();
$db = $database->connect();

$user_data = check_login($db);

if ($user_data['role'] !== 'Student') {
    header("Location: ../index.php");
    die;
}

$query = "SELECT * FROM students WHERE user_id = :user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $user_data['id']);
$stmt->execute();
$student_data = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">Student Dashboard</h1>
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-xl font-semibold mb-4">Welcome,
                <?php echo htmlspecialchars($student_data['first_name'] . ' ' . $student_data['last_name']); ?>
            </h2>
            <p class="text-gray-600">Grade Level: <?php echo htmlspecialchars($student_data['grade_level']); ?></p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <a href="view_violations.php" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <h2 class="text-xl font-semibold mb-2">View Violations</h2>
                <p class="text-gray-600">Check your reported violations</p>
            </a>
            <a href="schedule_counseling.php"
                class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <h2 class="text-xl font-semibold mb-2">Schedule Counseling</h2>
                <p class="text-gray-600">Book a counseling session</p>
            </a>
            <a href="view_academic_records.php"
                class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <h2 class="text-xl font-semibold mb-2">Academic Records</h2>
                <p class="text-gray-600">View your academic performance</p>
            </a>
        </div>
    </div>
</body>

</html>