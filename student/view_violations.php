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

$query = "SELECT s.id FROM students s WHERE s.user_id = :user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $user_data['id']);
$stmt->execute();
$student_id = $stmt->fetchColumn();

$query = "SELECT r.id, r.violation_type, r.description, r.date_of_incident, r.status, u.username as reported_by
          FROM rule_violation_reports r
          JOIN users u ON r.reported_by = u.id
          WHERE r.student_id = :student_id
          ORDER BY r.date_reported DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(':student_id', $student_id);
$stmt->execute();
$violations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Violations</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">View Violations</h1>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Your Reported Violations</h2>
            <?php if (empty($violations)): ?>
                <p class="text-gray-600">No violations reported.</p>
            <?php else: ?>
                <table class="w-full">
                    <thead>
                        <tr>
                            <th class="text-left py-2">Violation Type</th>
                            <th class="text-left py-2">Date of Incident</th>
                            <th class="text-left py-2">Reported By</th>
                            <th class="text-left py-2">Status</th>
                            <th class="text-left py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($violations as $violation): ?>
                            <tr>
                                <td class="py-2"><?php echo htmlspecialchars($violation['violation_type']); ?></td>
                                <td class="py-2"><?php echo htmlspecialchars($violation['date_of_incident']); ?></td>
                                <td class="py-2"><?php echo htmlspecialchars($violation['reported_by']); ?></td>
                                <td class="py-2"><?php echo htmlspecialchars($violation['status']); ?></td>
                                <td class="py-2">
                                    <a href="view_violation_details.php?id=<?php echo $violation['id']; ?>"
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