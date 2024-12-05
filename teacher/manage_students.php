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

$query = "SELECT s.id, s.first_name, s.last_name, s.grade_level, u.email
          FROM students s
          JOIN users u ON s.user_id = u.id
          ORDER BY s.last_name, s.first_name";
$stmt = $db->prepare($query);
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">Manage Students</h1>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Student List</h2>
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="text-left py-2">Name</th>
                        <th class="text-left py-2">Grade Level</th>
                        <th class="text-left py-2">Email</th>
                        <th class="text-left py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td class="py-2">
                                <?php echo htmlspecialchars($student['last_name'] . ', ' . $student['first_name']); ?>
                            </td>
                            <td class="py-2"><?php echo htmlspecialchars($student['grade_level']); ?></td>
                            <td class="py-2"><?php echo htmlspecialchars($student['email']); ?></td>
                            <td class="py-2">
                                <a href="view_student.php?id=<?php echo $student['id']; ?>"
                                    class="text-blue-500 hover:text-blue-700">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>