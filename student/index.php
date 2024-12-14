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
    <nav class="bg-blue-600 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">Student Guidance System</h1>
            <div>
                <span class="mr-4">Welcome, <?php echo htmlspecialchars($user_data['username']); ?></span>
                <a href="../logout.php?csrf_token=<?php echo $csrf_token; ?>" class="hover:underline">Logout</a>
            </div>
        </div>
    </nav>
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <h1 class="text-3xl font-bold mb-4">Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-blue-100 p-4 rounded">
                <h2 class="text-xl font-semibold mb-2">Academic Records</h2>
                <a href="academic_records.php" class="text-blue-500 hover:underline">View Records</a>
            </div>
            <div class="bg-green-100 p-4 rounded">
                <h2 class="text-xl font-semibold mb-2">Attendance</h2>
                <a href="attendance.php" class="text-green-500 hover:underline">View Attendance</a>
            </div>
            <div class="bg-yellow-100 p-4 rounded">
                <h2 class="text-xl font-semibold mb-2">Counseling Sessions</h2>
                <a href="counseling.php" class="text-yellow-500 hover:underline">View Sessions</a>
            </div>
            <div class="bg-red-100 p-4 rounded">
                <h2 class="text-xl font-semibold mb-2">Rule Violations</h2>
                <a href="violations.php" class="text-red-500 hover:underline">View Violations</a>
            </div>
            <div class="bg-purple-100 p-4 rounded">
                <h2 class="text-xl font-semibold mb-2">Notifications</h2>
                <a href="notifications.php" class="text-purple-500 hover:underline">View Notifications</a>
            </div>
        </div>
    </div>
</body>

</html>