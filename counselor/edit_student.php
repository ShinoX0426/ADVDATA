<?php
session_start();
require_once '../includes/database.php';
require_once '../includes/functions.php';
require_once '../includes/student.class.php';
require_once '../includes/user.class.php';

$database = new Database();
$db = $database->connect();

$user_data = check_login($db);

if ($user_data['role'] !== 'Counselor') {
    header("Location: ../index.php");
    die;
}

$student = new Student($db);
$user = new User($db);
$error = '';
$success = '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: student_records.php");
    exit();
}

$student_id = $_GET['id'];
$student_data = $student->getStudentById($student_id);

if (!$student_data) {
    header("Location: student_records.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token']);

    $updated_student = [
        'id' => $student_id,
        'first_name' => $_POST['first_name'] ?? '',
        'last_name' => $_POST['last_name'] ?? '',
        'date_of_birth' => $_POST['date_of_birth'] ?? '',
        'grade_level' => $_POST['grade_level'] ?? '',
    ];

    $updated_user = [
        'id' => $student_data['user_id'],
        'email' => $_POST['email'] ?? '',
    ];

    if ($student->updateStudent($updated_student) && $user->updateUser($updated_user)) {
        $success = "Student updated successfully!";
        $student_data = $student->getStudentById($student_id);
    } else {
        $error = "Error updating student. Please try again.";
    }
}

$csrf_token = generate_csrf_token();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <nav class="bg-blue-600 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-white text-2xl font-bold">Edit Student</h1>
            <div>
                <a href="student_records.php" class="text-white hover:underline mr-4">Back to Student Records</a>
                <a href="../logout.php?csrf_token=<?php echo $csrf_token; ?>"
                    class="text-white hover:underline">Logout</a>
            </div>
        </div>
    </nav>
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white shadow-md rounded-lg p-6">
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                    role="alert">
                    <span class="block sm:inline"><?php echo htmlspecialchars($success); ?></span>
                </div>
            <?php endif; ?>
            <form action="edit_student.php?id=<?php echo $student_id; ?>" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input type="email" id="email" name="email" required
                        value="<?php echo htmlspecialchars($student_data['email']); ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label for="first_name" class="block text-gray-700 text-sm font-bold mb-2">First Name</label>
                    <input type="text" id="first_name" name="first_name" required
                        value="<?php echo htmlspecialchars($student_data['first_name']); ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label for="last_name" class="block text-gray-700 text-sm font-bold mb-2">Last Name</label>
                    <input type="text" id="last_name" name="last_name" required
                        value="<?php echo htmlspecialchars($student_data['last_name']); ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label for="date_of_birth" class="block text-gray-700 text-sm font-bold mb-2">Date of Birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" required
                        value="<?php echo htmlspecialchars($student_data['date_of_birth']); ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label for="grade_level" class="block text-gray-700 text-sm font-bold mb-2">Grade Level</label>
                    <input type="number" id="grade_level" name="grade_level" required min="1" max="12"
                        value="<?php echo htmlspecialchars($student_data['grade_level']); ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit"
                    class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 focus:outline-none focus:shadow-outline">
                    Update Student
                </button>
            </form>
        </div>
    </div>
</body>

</html>