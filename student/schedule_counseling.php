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

$query = "SELECT id FROM students WHERE user_id = :user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $user_data['id']);
$stmt->execute();
$student_id = $stmt->fetchColumn();

$query = "SELECT id, username FROM users WHERE role = 'Counselor'";
$stmt = $db->prepare($query);
$stmt->execute();
$counselors = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token']);

    $counselor_id = $_POST['counselor_id'];
    $date_time = $_POST['date_time'];
    $notes = $_POST['notes'];

    $query = "INSERT INTO counseling_sessions (student_id, counselor_id, date_time, notes)
              VALUES (:student_id, :counselor_id, :date_time, :notes)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->bindParam(':counselor_id', $counselor_id);
    $stmt->bindParam(':date_time', $date_time);
    $stmt->bindParam(':notes', $notes);

    if ($stmt->execute()) {
        $success_message = "Counseling session scheduled successfully";
    } else {
        $error_message = "Unable to schedule counseling session";
    }
}

$csrf_token = generate_csrf_token();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Counseling</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">Schedule Counseling</h1>

        <?php if (isset($success_message)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $success_message; ?></span>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $error_message; ?></span>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="bg-white p-6 rounded-lg shadow-md">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="counselor_id" class="block text-sm font-medium text-gray-700 mb-1">Counselor</label>
                    <select id="counselor_id" name="counselor_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select a counselor</option>
                        <?php foreach ($counselors as $counselor): ?>
                            <option value="<?php echo $counselor['id']; ?>">
                                <?php echo htmlspecialchars($counselor['username']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="date_time" class="block text-sm font-medium text-gray-700 mb-1">Date and Time</label>
                    <input type="datetime-local" id="date_time" name="date_time" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                    <textarea id="notes" name="notes"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        rows="4"></textarea>
                </div>
            </div>
            <div class="mt-6">
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Schedule Counseling Session
                </button>
            </div>
        </form>
    </div>
</body>

</html>