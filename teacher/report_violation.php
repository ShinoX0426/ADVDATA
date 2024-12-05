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

$query = "SELECT id, first_name, last_name FROM students ORDER BY last_name, first_name";
$stmt = $db->prepare($query);
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token']);

    $student_id = $_POST['student_id'];
    $violation_type = $_POST['violation_type'];
    $description = $_POST['description'];
    $date_of_incident = $_POST['date_of_incident'];
    $reported_by = $user_data['id'];

    $query = "INSERT INTO rule_violation_reports (student_id, reported_by, violation_type, description, date_of_incident)
              VALUES (:student_id, :reported_by, :violation_type, :description, :date_of_incident)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->bindParam(':reported_by', $reported_by);
    $stmt->bindParam(':violation_type', $violation_type);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':date_of_incident', $date_of_incident);

    if ($stmt->execute()) {
        $success_message = "Violation report submitted successfully";
    } else {
        $error_message = "Unable to submit violation report";
    }
}

$csrf_token = generate_csrf_token();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Violation</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">Report Violation</h1>

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
                    <label for="student_id" class="block text-sm font-medium text-gray-700 mb-1">Student</label>
                    <select id="student_id" name="student_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select a student</option>
                        <?php foreach ($students as $student): ?>
                            <option value="<?php echo $student['id']; ?>">
                                <?php echo htmlspecialchars($student['last_name'] . ', ' . $student['first_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="violation_type" class="block text-sm font-medium text-gray-700 mb-1">Violation
                        Type</label>
                    <select id="violation_type" name="violation_type" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select violation type</option>
                        <option value="Minor Misconduct">Minor Misconduct</option>
                        <option value="Serious Misconduct">Serious Misconduct</option>
                        <option value="Academic Violation">Academic Violation</option>
                        <option value="Behavioral Issue">Behavioral Issue</option>
                        <option value="Attendance Problem">Attendance Problem</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="description" name="description" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        rows="4"></textarea>
                </div>
                <div>
                    <label for="date_of_incident" class="block text-sm font-medium text-gray-700 mb-1">Date of
                        Incident</label>
                    <input type="date" id="date_of_incident" name="date_of_incident" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="mt-6">
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Submit Report
                </button>
            </div>
        </form>
    </div>
</body>

</html>