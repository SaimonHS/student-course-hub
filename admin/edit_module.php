<?php
include 'auth.php';
include '../includes/db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header("Location: modules.php");
    exit();
}

$error = "";

$stmt = $conn->prepare("SELECT * FROM Modules WHERE ModuleID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: modules.php");
    exit();
}

$module = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $moduleName = trim($_POST['module_name']);
    $moduleLeaderId = ($_POST['module_leader_id'] !== '') ? (int) $_POST['module_leader_id'] : null;
    $description = trim($_POST['description']);

    if ($moduleName === '') {
        $error = "Module name is required.";
    } else {
        $update = $conn->prepare("UPDATE Modules
                                  SET ModuleName = ?, ModuleLeaderID = ?, Description = ?
                                  WHERE ModuleID = ?");
        $update->bind_param("sisi", $moduleName, $moduleLeaderId, $description, $id);

        if ($update->execute()) {
            header("Location: modules.php");
            exit();
        } else {
            $error = "Could not update module.";
        }
    }
}

$staff = $conn->query("SELECT StaffID, Name FROM Staff ORDER BY Name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Module</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="page-container">
    <div class="programme-card">
        <h1>Edit Module</h1>

        <?php if ($error !== ""): ?>
            <p><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Module Name</label><br>
            <input type="text" name="module_name" value="<?php echo htmlspecialchars($module['ModuleName']); ?>" required><br><br>

            <label>Module Leader</label><br>
            <select name="module_leader_id">
                <option value="">Select Leader</option>
                <?php while ($member = $staff->fetch_assoc()): ?>
                    <option value="<?php echo $member['StaffID']; ?>"
                        <?php echo ($module['ModuleLeaderID'] == $member['StaffID']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($member['Name']); ?>
                    </option>
                <?php endwhile; ?>
            </select><br><br>

            <label>Description</label><br>
            <textarea name="description" rows="5" cols="50"><?php echo htmlspecialchars($module['Description']); ?></textarea><br><br>

            <button type="submit">Update Module</button>
        </form>

        <p><a href="modules.php">Back to Modules</a></p>
    </div>
</div>

</body>
</html>