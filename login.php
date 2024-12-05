<?php
session_start();
require_once 'includes/database.php';
require_once 'includes/user.class.php';
require_once 'includes/functions.php';

$database = new Database();
$db = $database->connect();
$user = new User($db);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token']);

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $login_result = $user->login($username, $password);

    if ($login_result['success']) {
        $_SESSION['user_id'] = $login_result['user_id'];
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $login_result['role'];

        // Redirect based on user role
        switch ($_SESSION['role']) {
            case 'Admin':
                header("Location: admin/index.php");
                break;
            case 'Teacher':
                header("Location: teacher/index.php");
                break;
            case 'Student':
                header("Location: student/index.php");
                break;
            case 'Counselor':
                header("Location: counselor/index.php");
                break;
            default:
                header("Location: index.php");
        }
        exit();
    } else {
        $error = "Invalid username or password";
    }
}

$csrf_token = generate_csrf_token();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Student Guidance System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-96">
        <h1 class="text-2xl font-bold mb-6 text-center">Login</h1>
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>
        <form action="login.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <div class="mb-4">
                <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                <input type="text" id="username" name="username" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input type="password" id="password" name="password" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit"
                class="w-full bg-blue-500 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline hover:bg-blue-600 transition duration-300">
                Login
            </button>
        </form>
    </div>
</body>

</html>