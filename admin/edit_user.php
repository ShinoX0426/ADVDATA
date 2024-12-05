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

// Get user ID from URL parameter
$user_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$user_id) {
    header("Location: manage_users.php");
    exit();
}

// Fetch user data
$user_data = $user->getUserById($user_id);

if (!$user_data) {
    header("Location: manage_users.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token']);

    $edit_user = [
        'id' => $user_id,
        'username' => $_POST['username'],
        'email' => $_POST['email'],
        'role' => $_POST['role'],
        'full_name' => $_POST['full_name']
    ];

    if (!empty($_POST['password'])) {
        $edit_user['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    $user->updateUser($edit_user);
    header("Location: manage_users.php");
    exit();
}

$csrf_token = generate_csrf_token();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - DPLMHS Admin</title>
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
                        <a href="manage_users.php" class="mr-4 hover:underline">Manage Users</a>
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
            <h1 class="text-3xl font-bold mb-8">Edit User</h1>

            <!-- Edit User Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <form action="edit_user.php?id=<?php echo $user_id; ?>" method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                            <input type="text" id="username" name="username"
                                value="<?php echo htmlspecialchars($user_data['username']); ?>" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Password (leave blank
                                to keep current)</label>
                            <input type="password" id="password" name="password"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" id="email" name="email"
                                value="<?php echo htmlspecialchars($user_data['email']); ?>" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                            <select id="role" name="role" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <option value="Admin" <?php echo $user_data['role'] === 'Admin' ? 'selected' : ''; ?>>
                                    Admin</option>
                                <option value="Teacher" <?php echo $user_data['role'] === 'Teacher' ? 'selected' : ''; ?>>
                                    Teacher</option>
                                <option value="Student" <?php echo $user_data['role'] === 'Student' ? 'selected' : ''; ?>>
                                    Student</option>
                                <option value="Counselor" <?php echo $user_data['role'] === 'Counselor' ? 'selected' : ''; ?>>Counselor</option>
                            </select>
                        </div>
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" id="full_name" name="full_name"
                                value="<?php echo htmlspecialchars($user_data['full_name']); ?>" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                    </div>
                    <div>
                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition duration-300">
                            Update User
                        </button>
                        <a href="manage_users.php"
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