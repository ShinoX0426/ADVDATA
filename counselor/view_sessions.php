<?php
session_start();
require_once '../includes/database.php';
require_once '../includes/functions.php';

$database = new Database();
$db = $database->connect();

$user_data = check_login($db);

if ($user_data['role'] !== 'Counselor') {
    header("Location: ../index.php");
    die;
}

$query = "SELECT cs.id, s.first_name, s.last_name, cs.date_time, cs.status
          FROM counseling_sessions cs
          JOIN students s ON cs.student_id = s.id
          WHERE cs.counselor_id = :counselor_id
          ORDER BY cs.date_time DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(':counselor_id', $user_data['id']);
$stmt->execute();
$sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Counseling Sessions</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">View Counseling Sessions</h1>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Your Counseling Sessions</h2>
            <?php if (empty($sessions)): ?>
                <p class="text-gray-600">No counseling sessions scheduled.</p>
            <?php else: ?>
                <table class="w-full">
                    <thead>
                        <tr>
                            <th class="text-left py-2">Student</th>
                            <th class="text-left py-2">Date and Time</th>
                            <th class="text-left py-2">Status</th>
                            <th class="text-left py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sessions as $session): ?>
                            <tr>
                                <td class="py-2">
                                    <?php echo htmlspecialchars($session['last_name'] . ', ' . $session['first_name']); ?>
                                </td>
                                <td class="py-2"><?php echo htmlspecialchars($session['date_time']); ?></td>
                                <td class="py-2"><?php echo htmlspecialchars($session['status']); ?></td>
                                <td class="py-2">
                                    <a href="session_details.php?id=<?php echo $session['id']; ?>"
                                        class="text-blue-500 hover:text-blue-700">View Details</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>