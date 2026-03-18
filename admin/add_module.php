<?php
include 'auth.php';
include '../includes/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $moduleName = trim($_POST['module_name']);
    $moduleLeaderId = ($_POST['module_leader_id'] !== '') ? (int) $_POST['module_leader_id'] : null;
    $description = trim($_POST['description']);

    if ($moduleName === '') {
        $error = "Module name is required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO Modules (ModuleName, ModuleLeaderID, Description)
                                VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $moduleName, $moduleLeaderId, $description);

        if ($stmt->execute()) {
            header("Location: modules.php");
            exit();
        } else {
            $error = "Could not add module.";
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
    <title>Add Module</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="page-container">
    <div class="programme-card">
        <h1>Add Module</h1>

        <?php if ($error !== ""): ?>
            <p><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Module Name</label><br>
            <input type="text" name="module_name" required><br><br>

            <label>Module Leader</label><br>
            <select name="module_leader_id">
                <option value="">Select Leader</option>
                <?php while ($member = $staff->fetch_assoc()): ?>
                    <option value="<?php echo $member['StaffID']; ?>">
                        <?php echo htmlspecialchars($member['Name']); ?>
                    </option>
                <?php endwhile; ?>
            </select><br><br>

            <label>Description</label><br>
            <textarea name="description" rows="5" cols="50"></textarea><br><br>

            <button type="submit">Add Module</button>
        </form>

        <p><a href="modules.php">Back to Modules</a></p>
    </div>
</div>

</body>
</html>