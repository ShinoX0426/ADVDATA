<?php
session_start();
require_once '../includes/database.php';
require_once '../includes/user.class.php';
require_once '../includes/functions.php';
require_once '../includes/student.class.php';
require_once '../includes/counseling_session.class.php';
require_once '../includes/violation_report.class.php';

$database = new Database();
$db = $database->connect();

$user_data = check_login($db);

if ($user_data['role'] !== 'Counselor') {
    header("Location: ../index.php");
    die;
}

$student = new Student($db);
$session = new CounselingSession($db);
$violation = new ViolationReport($db);

$total_students = $student->getTotalStudents();
$upcoming_sessions = $session->getUpcomingSessions($user_data['id'], 5);
$recent_violations = $violation->getRecentViolations(5);

$csrf_token = generate_csrf_token();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Counselor Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
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

    <div class="container mx-auto px-4 py-8">
        <h2 class="text-3xl font-bold mb-8">Counselor Dashboard</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">Total Students</h3>
                <p class="text-3xl font-bold text-blue-600"><?php echo $total_students; ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">Upcoming Sessions</h3>
                <p class="text-3xl font-bold text-green-600"><?php echo count($upcoming_sessions); ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">Recent Violations</h3>
                <p class="text-3xl font-bold text-red-600"><?php echo count($recent_violations); ?></p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-4">Quick Actions</h3>
                <div class="grid grid-cols-2 gap-4">
                    <a href="add_student.php"
                        class="bg-blue-500 text-white p-4 rounded-lg text-center hover:bg-blue-600 transition-colors">
                        <i class="fas fa-user-plus mb-2"></i>
                        <span class="block">Add Student</span>
                    </a>
                    <a href="schedule_session.php"
                        class="bg-green-500 text-white p-4 rounded-lg text-center hover:bg-green-600 transition-colors">
                        <i class="fas fa-calendar-plus mb-2"></i>
                        <span class="block">Schedule Session</span>
                    </a>
                    <a href="report_violation.php"
                        class="bg-red-500 text-white p-4 rounded-lg text-center hover:bg-red-600 transition-colors">
                        <i class="fas fa-exclamation-triangle mb-2"></i>
                        <span class="block">Report Violation</span>
                    </a>
                    <a href="generate_report.php"
                        class="bg-purple-500 text-white p-4 rounded-lg text-center hover:bg-purple-600 transition-colors">
                        <i class="fas fa-chart-bar mb-2"></i>
                        <span class="block">Generate Report</span>
                    </a>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-4">Upcoming Sessions</h3>
                <?php if (empty($upcoming_sessions)): ?>
                    <p class="text-gray-600">No upcoming sessions scheduled.</p>
                <?php else: ?>
                    <ul class="space-y-2">
                        <?php foreach ($upcoming_sessions as $session): ?>
                            <li class="flex justify-between items-center">
                                <span><?php echo htmlspecialchars($session['student_name']); ?></span>
                                <span
                                    class="text-sm text-gray-600"><?php echo date('M d, Y H:i', strtotime($session['date_time'])); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <a href="view_sessions.php" class="mt-4 inline-block text-blue-600 hover:underline">View all
                    sessions</a>
            </div>
        </div>

        <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold mb-4">Recent Violation Reports</h3>
            <?php if (empty($recent_violations)): ?>
                <p class="text-gray-600">No recent violation reports.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="p-2">Student</th>
                                <th class="p-2">Violation Type</th>
                                <th class="p-2">Date</th>
                                <th class="p-2">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_violations as $violation): ?>
                                <tr class="border-b">
                                    <td class="p-2"><?php echo htmlspecialchars($violation['student_name']); ?></td>
                                    <td class="p-2"><?php echo htmlspecialchars($violation['violation_type']); ?></td>
                                    <td class="p-2"><?php echo date('M d, Y', strtotime($violation['date_of_incident'])); ?>
                                    </td>
                                    <td class="p-2">
                                        <span
                                            class="px-2 py-1 rounded text-sm 
                                            <?php echo $violation['status'] === 'Pending' ? 'bg-yellow-200 text-yellow-800' :
                                                ($violation['status'] === 'In Progress' ? 'bg-blue-200 text-blue-800' : 'bg-green-200 text-green-800'); ?>">
                                            <?php echo htmlspecialchars($violation['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <a href="violation_reports.php" class="mt-4 inline-block text-blue-600 hover:underline">View all violation
                    reports</a>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // You can add any necessary JavaScript here
    </script>
</body>

</html>