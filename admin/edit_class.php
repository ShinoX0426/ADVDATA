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

$admin_name = $_SESSION['username'];

// Get class ID from URL parameter
$class_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$class_id) {
    header("Location: manage_classes.php");
    exit();
}

// Fetch class data
$class_data = $user->getClassById($class_id);

if (!$class_data) {
    header("Location: manage_classes.php");
    exit();
}

// Fetch teachers for the dropdown
$teachers = $user->getUsersByRole('Teacher');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token']);

    $edit_class = [
        'id' => $class_id,
        'name' => $_POST['class_name'],
        'grade_level' => $_POST['grade_level'],
        'section' => $_POST['section'],
        'teacher_id' => $_POST['teacher_id']
    ];

    $user->updateClass($edit_class);
    header("Location: manage_classes.php");
    exit();
}

$csrf_token = generate_csrf_token();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Class - DPLMHS Admin</title>
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
                        <a href="manage_classes.php" class="mr-4 hover:underline">Manage Classes</a>
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
            <h1 class="text-3xl font-bold mb-8">Edit Class</h1>

            <!-- Edit Class Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <form action="edit_class.php?id=<?php echo $class_id; ?>" method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="class_name" class="block text-sm font-medium text-gray-700">Class Name</label>
                            <input type="text" id="class_name" name="class_name"
                                value="<?php echo htmlspecialchars($class_data['name']); ?>" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="grade_level" class="block text-sm font-medium text-gray-700">Grade Level</label>
                            <input type="number" id="grade_level" name="grade_level"
                                value="<?php echo htmlspecialchars($class_data['grade_level']); ?>" required min="7"
                                max="12"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="section" class="block text-sm font-medium text-gray-700">Section</label>
                            <input type="text" id="section" name="section"
                                value="<?php echo htmlspecialchars($class_data['section']); ?>" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="teacher_id" class="block text-sm font-medium text-gray-700">Assigned
                                Teacher</label>
                            <select id="teacher_id" name="teacher_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?php echo $teacher['id']; ?>" <?php echo $teacher['id'] == $class_data['teacher_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($teacher['full_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div>
                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition duration-300">
                            Update Class
                        </button>
                        <a href="manage_classes.php"
                            class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition duration-300">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-200 text-center py-4">
            <p>&copy; 2023 Don Pablo Lorenzo Memorial High School. All rights reserved.</p>
        </footer>
    </div>
</body>

</html>