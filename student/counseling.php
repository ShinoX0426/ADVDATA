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

$student_id = $_SESSION['user_id'];

// Fetch counseling sessions
$query = "SELECT cs.*, u.full_name as counselor_name 
          FROM counseling_sessions cs 
          JOIN users u ON cs.counselor_id = u.id 
          WHERE cs.student_id = :student_id 
          ORDER BY cs.date_time DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(':student_id', $student_id);
$stmt->execute();
$counseling_sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Counseling Sessions - Student Guidance System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-4">Counseling Sessions</h1>
        <a href="index.php"
            class="mb-4 inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Back to
            Dashboard</a>
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="px-4 py-2">Date & Time</th>
                        <th class="px-4 py-2">Counselor</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($counseling_sessions as $session): ?>
                        <tr>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($session['date_time']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($session['counselor_name']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($session['status']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($session['notes']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>