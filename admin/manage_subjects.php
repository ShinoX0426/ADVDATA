<?php
session_start();
require_once '../includes/database.php';
require_once '../includes/user.class.php';
require_once '../includes/functions.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

$database = new Database();
$db = $database->connect();
$user = new User($db);

// Handle subject actions (add, delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token']);

    if (isset($_POST['add_subject'])) {
        $subject_data = [
            'name' => trim($_POST['subject_name']),
            'description' => trim($_POST['description']),
            'grade_level' => (int) $_POST['grade_level']
        ];

        if ($user->addSubject($subject_data)) {
            $_SESSION['success_message'] = "Subject added successfully!";
        } else {
            $_SESSION['error_message'] = "Error adding subject.";
        }
    } elseif (isset($_POST['delete_subject'])) {
        $subject_id = (int) $_POST['subject_id'];
        if ($user->deleteSubject($subject_id)) {
            $_SESSION['success_message'] = "Subject deleted successfully!";
        } else {
            $_SESSION['error_message'] = "Error deleting subject.";
        }
    }

    header("Location: manage_subjects.php");
    exit();
}

// Fetch subjects for display
$subjects = $user->getAllSubjects();
$csrf_token = generate_csrf_token();

// Get admin name
$admin_name = $_SESSION['full_name'] ?? 'Admin';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subjects - DPLMHS Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <span class="font-bold text-xl">DPLMHS Admin</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="index.php" class="hover:underline">Dashboard</a>
                    <span>Welcome, <?php echo htmlspecialchars($admin_name); ?></span>
                    <a href="../logout.php?csrf_token=<?php echo $csrf_token; ?>"
                        class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded transition duration-300">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">Manage Subjects</h1>

        <?php
        if (isset($_SESSION['success_message'])) {
            echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4' role='alert'>";
            echo htmlspecialchars($_SESSION['success_message']);
            echo "</div>";
            unset($_SESSION['success_message']);
        }

        if (isset($_SESSION['error_message'])) {
            echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>";
            echo htmlspecialchars($_SESSION['error_message']);
            echo "</div>";
            unset($_SESSION['error_message']);
        }
        ?>

        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-bold mb-4">Add New Subject</h2>
            <form action="manage_subjects.php" method="POST" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="subject_name" class="block text-sm font-medium text-gray-700">Subject Name</label>
                        <input type="text" id="subject_name" name="subject_name" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>
                    <div>
                        <label for="grade_level" class="block text-sm font-medium text-gray-700">Grade Level</label>
                        <input type="number" id="grade_level" name="grade_level" required min="7" max="12"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea id="description" name="description" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"></textarea>
                    </div>
                </div>
                <div>
                    <button type="submit" name="add_subject"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition duration-300">
                        Add Subject
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-4">Subject List</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Subject Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Grade Level</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($subjects as $subject): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php echo htmlspecialchars($subject['name']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php echo htmlspecialchars($subject['grade_level']); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php echo htmlspecialchars($subject['description']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="edit_subject.php?id=<?php echo $subject['id']; ?>"
                                        class="text-blue-600 hover:text-blue-900 mr-2">Edit</a>
                                    <form action="manage_subjects.php" method="POST" class="inline">
                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                        <input type="hidden" name="subject_id" value="<?php echo $subject['id']; ?>">
                                        <button type="submit" name="delete_subject" class="text-red-600 hover:text-red-900"
                                            onclick="return confirm('Are you sure you want to delete this subject?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <footer class="bg-gray-200 text-center py-4 mt-auto">
        <p>&copy; 2023 Don Pablo Lorenzo Memorial High School. All rights reserved.</p>
    </footer>
</body>

</html>