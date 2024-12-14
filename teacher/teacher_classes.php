<?php
session_start();
require_once 'database.php';
require_once 'functions.php';
require_once 'user.class.php';

$database = new Database();
$db = $database->connect();
$user = new User($db);

$user_data = check_login($db);

if ($_SESSION['role'] !== 'Teacher') {
    header("Location: index.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];

// Fetch teacher's classes
$query = "SELECT * FROM classes WHERE teacher_id = :teacher_id ORDER BY name";
$stmt = $db->prepare($query);
$stmt->bindParam(':teacher_id', $teacher_id);
$stmt->execute();
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Classes - Teacher Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-4">My Classes</h1>
        <a href="dashboard.php"
            class="mb-4 inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Back to
            Dashboard</a>
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <?php if (empty($classes)): ?>
                <p class="text-gray-600">You are not assigned to any classes yet.</p>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php foreach ($classes as $class): ?>
                        <div class="border rounded p-4">
                            <h2 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($class['name']); ?></h2>
                            <p>Grade Level: <?php echo htmlspecialchars($class['grade_level']); ?></p>
                            <p>Section: <?php echo htmlspecialchars($class['section']); ?></p>
                            <a href="class_details.php?id=<?php echo $class['id']; ?>"
                                class="mt-2 inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">View
                                Details</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>