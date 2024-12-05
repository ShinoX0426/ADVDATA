<?php
session_start();
require_once '../includes/database.php';
require_once '../includes/user.class.php';
require_once '../includes/functions.php';


// Assuming a User class exists for database interaction


// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

$database = new Database();
$db = $database->connect();
$user = new User($db);
$teachers = $user->getUsersByRole('Teacher');

$admin_name = $_SESSION['username'];

// Handle class actions (add, edit, delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token']);

    if (isset($_POST['add_class'])) {
        // Add class logic
        $new_class = [
            'name' => $_POST['class_name'],
            'grade_level' => $_POST['grade_level'],
            'section' => $_POST['section'],
            'teacher_id' => $_POST['teacher_id']
        ];
        $user->addClass($new_class);
    } elseif (isset($_POST['edit_class'])) {
        // Edit class logic
        $edit_class = [
            'id' => $_POST['class_id'],
            'name' => $_POST['class_name'],
            'grade_level' => $_POST['grade_level'],
            'section' => $_POST['section'],
            'teacher_id' => $_POST['teacher_id']
        ];
        $user->updateClass($edit_class);
    } elseif (isset($_POST['delete_class'])) {
        // Delete class logic
        $user->deleteClass($_POST['class_id']);
    }
}

// Fetch classes for display
$classes = $user->getAllClasses();

// Fetch teachers for the dropdown
$teachers = $user->getUsersByRole('Teacher');

$csrf_token = generate_csrf_token();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Classes - DPLMHS Admin</title>
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
            <h1 class="text-3xl font-bold mb-8">Manage Classes</h1>

            <!-- Add Class Form -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-2xl font-bold mb-4">Add New Class</h2>
                <form action="manage_classes.php" method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="class_name" class="block text-sm font-medium text-gray-700">Class Name</label>
                            <input type="text" id="class_name" name="class_name" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="grade_level" class="block text-sm font-medium text-gray-700">Grade Level</label>
                            <input type="number" id="grade_level" name="grade_level" required min="7" max="12"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="section" class="block text-sm font-medium text-gray-700">Section</label>
                            <input type="text" id="section" name="section" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="teacher_id" class="block text-sm font-medium text-gray-700">Assigned
                                Teacher</label>
                            <select id="teacher_id" name="teacher_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <option value="">Select a teacher</option>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?php echo $teacher['id']; ?>">
                                        <?php echo htmlspecialchars($teacher['full_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div>
                        <button type="submit" name="add_class"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition duration-300">
                            Add Class
                        </button>
                    </div>
                </form>
            </div>

            <!-- Class List -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold mb-4">Class List</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Class Name</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Grade Level</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Section</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Assigned Teacher</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($classes as $class): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($class['name']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php echo htmlspecialchars($class['grade_level']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php echo htmlspecialchars($class['section']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php echo htmlspecialchars($class['teacher_name']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="edit_class.php?id=<?php echo $class['id']; ?>"
                                            class="text-blue-600 hover:text-blue-900 mr-2">Edit</a>
                                        <form action="manage_classes.php" method="POST" class="inline">
                                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                            <input type="hidden" name="class_id" value="<?php echo $class['id']; ?>">
                                            <button type="submit" name="delete_class"
                                                class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('Are you sure you want to delete this class?')">Delete</button>
                                        </form>
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