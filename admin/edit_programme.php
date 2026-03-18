<?php
include 'auth.php';
include '../includes/db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header("Location: programmes.php");
    exit();
}

$error = "";

$stmt = $conn->prepare("SELECT * FROM Programmes WHERE ProgrammeID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: programmes.php");
    exit();
}

$programme = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $programmeName = trim($_POST['programme_name']);
    $levelId = (int) $_POST['level_id'];
    $programmeLeaderId = ($_POST['programme_leader_id'] !== '') ? (int) $_POST['programme_leader_id'] : null;
    $description = trim($_POST['description']);

    if ($programmeName === '' || $levelId <= 0) {
        $error = "Programme name and level are required.";
    } else {
        $update = $conn->prepare("UPDATE Programmes
                                  SET ProgrammeName = ?, LevelID = ?, ProgrammeLeaderID = ?, Description = ?
                                  WHERE ProgrammeID = ?");
        $update->bind_param("siisi", $programmeName, $levelId, $programmeLeaderId, $description, $id);

        if ($update->execute()) {
            header("Location: programmes.php");
            exit();
        } else {
            $error = "Could not update programme.";
        }
    }
}

$levels = $conn->query("SELECT LevelID, LevelName FROM Levels ORDER BY LevelName ASC");
$staff = $conn->query("SELECT StaffID, Name FROM Staff ORDER BY Name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Programme</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="page-container">
    <div class="programme-card">
        <h1>Edit Programme</h1>

        <?php if ($error !== ""): ?>
            <p><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Programme Name</label><br>
            <input type="text" name="programme_name" value="<?php echo htmlspecialchars($programme['ProgrammeName']); ?>" required><br><br>

            <label>Level</label><br>
            <select name="level_id" required>
                <?php while ($level = $levels->fetch_assoc()): ?>
                    <option value="<?php echo $level['LevelID']; ?>"
                        <?php echo ($programme['LevelID'] == $level['LevelID']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($level['LevelName']); ?>
                    </option>
                <?php endwhile; ?>
            </select><br><br>

            <label>Programme Leader</label><br>
            <select name="programme_leader_id">
                <option value="">Select Leader</option>
                <?php while ($member = $staff->fetch_assoc()): ?>
                    <option value="<?php echo $member['StaffID']; ?>"
                        <?php echo ($programme['ProgrammeLeaderID'] == $member['StaffID']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($member['Name']); ?>
                    </option>
                <?php endwhile; ?>
            </select><br><br>

            <label>Description</label><br>
            <textarea name="description" rows="5" cols="50"><?php echo htmlspecialchars($programme['Description']); ?></textarea><br><br>

            <button type="submit">Update Programme</button>
        </form>

        <p><a href="programmes.php">Back to Programmes</a></p>
    </div>
</div>

</body>
</html>