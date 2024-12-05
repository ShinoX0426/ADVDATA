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

$query = "SELECT r.id, s.first_name, s.last_name, r.violation_type, r.date_of_incident, r.status
          FROM rule_violation_reports r
          JOIN students s ON r.student_id = s.id
          WHERE r.reported_by = :teacher_id
          ORDER BY r.date_reported DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(':teacher_id', $user_data['id']);
$stmt->execute();
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Reports</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">View Reports</h1>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Submitted Violation Reports</h2>
            <?php if (empty($reports)): ?>
                <p class="text-gray-600">No reports submitted yet.</p>
            <?php else: ?>
                <table class="w-full">
                    <thead>
                        <tr>
                            <th class="text-left py-2">Student</th>
                            <th class="text-left py-2">Violation Type</th>
                            <th class="text-left py-2">Date of Incident</th>
                            <th class="text-left py-2">Status</th>
                            <th class="text-left py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reports as $report): ?>
                            <tr>
                                <td class="py-2">
                                    <?php echo htmlspecialchars($report['last_name'] . ', ' . $report['first_name']); ?>
                                </td>
                                <td class="py-2"><?php echo htmlspecialchars($report['violation_type']); ?></td>
                                <td class="py-2"><?php echo htmlspecialchars($report['date_of_incident']); ?></td>
                                <td class="py-2"><?php echo htmlspecialchars($report['status']); ?></td>
                                <td class="py-2">
                                    <a href="view_report.php?id=<?php echo $report['id']; ?>"
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