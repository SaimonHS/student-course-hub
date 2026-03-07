<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request.");
}

$programmeId = isset($_POST['programme_id']) ? (int) $_POST['programme_id'] : 0;
$studentName = isset($_POST['student_name']) ? trim($_POST['student_name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';

if ($programmeId <= 0) {
    die("Invalid programme.");
}

if (empty($studentName) || empty($email)) {
    die("All fields are required.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email address.");
}

$stmtCheckProgramme = $conn->prepare("SELECT ProgrammeID, ProgrammeName FROM Programmes WHERE ProgrammeID = ?");

if (!$stmtCheckProgramme) {
    die("Prepare failed (programme check): " . $conn->error);
}

$stmtCheckProgramme->bind_param("i", $programmeId);
$stmtCheckProgramme->execute();
$resultProgramme = $stmtCheckProgramme->get_result();

if ($resultProgramme->num_rows === 0) {
    die("Programme not found.");
}

$programme = $resultProgramme->fetch_assoc();

// duplication fix
$checkDuplicate = $conn->prepare("SELECT InterestID FROM InterestedStudents WHERE ProgrammeID = ? AND Email = ?");
$checkDuplicate->bind_param("is", $programmeId, $email);
$checkDuplicate->execute();
$duplicateResult = $checkDuplicate->get_result();

if ($duplicateResult->num_rows > 0) {
    die("You have already registered interest in this programme.");
}

// data entry
$stmt = $conn->prepare("INSERT INTO InterestedStudents (ProgrammeID, StudentName, Email) VALUES (?, ?, ?)");

if (!$stmt) {
    die("Prepare failed (insert): " . $conn->error);
}

$stmt->bind_param("iss", $programmeId, $studentName, $email);

$success = $stmt->execute();

if (!$success) {
    die("Execute failed: " . $stmt->error);
}

if (!$success) {
    die("Execute failed: " . $stmt->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interest Registration</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="programme-card">
    <h1>Thank You!</h1>
    <p>Your interest in <strong><?php echo htmlspecialchars($programme['ProgrammeName']); ?></strong> has been registered successfully.</p>
    <p>Name: <?php echo htmlspecialchars($studentName); ?></p>
    <p>Email: <?php echo htmlspecialchars($email); ?></p>
    <p><a href="programme.php?id=<?php echo $programmeId; ?>">Go back to programme</a></p>
    <p><a href="index.php">Back to homepage</a></p>
</div>

</body>
</html>