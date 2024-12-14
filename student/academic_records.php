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

$query = "SELECT id FROM students WHERE user_id = :user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $user_data['id']);
$stmt->execute();
$student_id = $stmt->fetchColumn();

$query = "SELECT * FROM academic_records WHERE student_id = :student_id ORDER BY academic_year DESC, semester DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(':student_id', $student_id);
$stmt->execute();
$academic_records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Records</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">Academic Records</h1>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Your Academic Performance</h2>
            <?php if (empty($academic_records)): ?>
                <p class="text-gray-600">No academic records found.</p>
            <?php else: ?>
                <table class="w-full">
                    <thead>
                        <tr>
                            <th class="text-left py-2">Academic Year</th>
                            <th class="text-left py-2">Semester</th>
                            <th class="text-left py-2">Subject</th>
                            <th class="text-left py-2">Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($academic_records as $record): ?>
                            <tr>
                                <td class="py-2"><?php echo htmlspecialchars($record['academic_year']); ?></td>
                                <td class="py-2"><?php echo htmlspecialchars($record['semester']); ?></td>
                                <td class="py-2"><?php echo htmlspecialchars($record['subject']); ?></td>
                                <td class="py-2"><?php echo htmlspecialchars($record['grade']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>