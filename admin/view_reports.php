<?php
session_start();
require_once '../includes/database.php';
require_once '../includes/user.class.php';
require_once '../includes/report.class.php';
require_once '../includes/functions.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

$database = new Database();
$db = $database->connect();
$user = new User($db);
$report = new Report($db);

$admin_name = $_SESSION['username'];

// Fetch reports (this is a placeholder - you'll need to implement this function)

$csrf_token = generate_csrf_token();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Reports - DPLMHS Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <!-- Navigation -->
        <nav class="bg-blue-600 text-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center">
                        <i class="fas fa-school text-2xl mr-2"></i>
                        <span class="font-bold text-xl">DPLMHS Admin</span>
                    </div>
                    <div class="flex items-center">
                        <a href="index.php" class="mr-4 hover:underline">Dashboard</a>
                        <span class="mr-4">Welcome, <?php echo htmlspecialchars($admin_name); ?></span>
                        <a href="../logout.php?csrf_token=<?php echo $csrf_token; ?>"
                            class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded transition duration-300">
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-grow container mx-auto px-4 py-8">
            <h1 class="text-3xl font-bold mb-8">View Reports</h1>

            <!-- Report Filters -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-2xl font-bold mb-4">Filter Reports</h2>
                <form action="" method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="report_type" class="block text-sm font-medium text-gray-700">Report Type</label>
                            <select id="report_type" name="report_type"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <option value="">All Types</option>
                                <option value="academic">Academic</option>
                                <option value="behavioral">Behavioral</option>
                                <option value="attendance">Attendance</option>
                            </select>
                        </div>
                        <div>
                            <label for="date_from" class="block text-sm font-medium text-gray-700">Date From</label>
                            <input type="date" id="date_from" name="date_from"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="date_to" class="block text-sm font-medium text-gray-700">Date To</label>
                            <input type="date" id="date_to" name="date_to"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                    </div>
                    <div>
                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition duration-300">
                            Apply Filters
                        </button>
                    </div>
                </form>
            </div>

            <!-- Reports Table -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold mb-4">Reports</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ID</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Type</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Subject</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Submitted By</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($reports as $report): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($report['id']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($report['type']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php echo htmlspecialchars($report['subject']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($report['date']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php echo htmlspecialchars($report['submitted_by']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="view_report.php?id=<?php echo $report['id']; ?>"
                                            class="text-blue-600 hover:text-blue-900 mr-2">View</a>
                                        <a href="edit_report.php?id=<?php echo $report['id']; ?>"
                                            class="text-green-600 hover:text-green-900 mr-2">Edit</a>
                                        <a href="delete_report.php?id=<?php echo $report['id']; ?>&csrf_token=<?php echo $csrf_token; ?>"
                                            class="text-red-600 hover:text-red-900"
                                            onclick="return confirm('Are you sure you want to delete this report?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-200 text-center py-4">
            <p>&copy; 2023 Don Pablo Lorenzo Memorial High School. All rights reserved.</p>
        </footer>
    </div>
</body>

</html>