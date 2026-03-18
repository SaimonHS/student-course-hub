<?php
include 'auth.php';
include '../includes/db.php';

$programmeId = isset($_POST['programme_id']) ? (int) $_POST['programme_id'] : (isset($_GET['programme_id']) ? (int) $_GET['programme_id'] : 0);

if ($programmeId <= 0) {
    header("Location: programmes.php");
    exit();
}

$message = "";
$error = "";

// Add assignment
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'add') {
    $moduleId = (int) $_POST['module_id'];
    $year = (int) $_POST['year'];

    if ($moduleId <= 0 || $year <= 0) {
        $error = "Module and year are required.";
    } else {
        $check = $conn->prepare("SELECT ProgrammeModuleID
                                 FROM ProgrammeModules
                                 WHERE ProgrammeID = ? AND ModuleID = ? AND Year = ?");
        $check->bind_param("iii", $programmeId, $moduleId, $year);
        $check->execute();
        $checkResult = $check->get_result();

        if ($checkResult->num_rows > 0) {
            $error = "This module is already assigned to this programme for that year.";
        } else {
            $insert = $conn->prepare("INSERT INTO ProgrammeModules (ProgrammeID, ModuleID, Year)
                                      VALUES (?, ?, ?)");
            $insert->bind_param("iii", $programmeId, $moduleId, $year);

            if ($insert->execute()) {
                $message = "Module assigned successfully.";
            } else {
                $error = "Could not assign module.";
            }
        }
    }
}

// Remove assignment
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'remove') {
    $programmeModuleId = (int) $_POST['programme_module_id'];

    if ($programmeModuleId > 0) {
        $delete = $conn->prepare("DELETE FROM ProgrammeModules
                                  WHERE ProgrammeModuleID = ? AND ProgrammeID = ?");
        $delete->bind_param("ii", $programmeModuleId, $programmeId);
        $delete->execute();
        $message = "Module removed from programme.";
    }
}

// Programme info
$programmeStmt = $conn->prepare("SELECT ProgrammeName FROM Programmes WHERE ProgrammeID = ?");
$programmeStmt->bind_param("i", $programmeId);
$programmeStmt->execute();
$programmeResult = $programmeStmt->get_result();

if ($programmeResult->num_rows === 0) {
    header("Location: programmes.php");
    exit();
}

$programme = $programmeResult->fetch_assoc();

// Current assignments
$assignedStmt = $conn->prepare("SELECT pm.ProgrammeModuleID, pm.Year, m.ModuleName, s.Name AS ModuleLeader
                                FROM ProgrammeModules pm
                                JOIN Modules m ON pm.ModuleID = m.ModuleID
                                LEFT JOIN Staff s ON m.ModuleLeaderID = s.StaffID
                                WHERE pm.ProgrammeID = ?
                                ORDER BY pm.Year ASC, m.ModuleName ASC");
$assignedStmt->bind_param("i", $programmeId);
$assignedStmt->execute();
$assignedResult = $assignedStmt->get_result();

// All modules
$allModules = $conn->query("SELECT ModuleID, ModuleName FROM Modules ORDER BY ModuleName ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Programme Modules</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="page-container">
    <h1>Manage Modules for <?php echo htmlspecialchars($programme['ProgrammeName']); ?></h1>

    <?php if ($message !== ""): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <?php if ($error !== ""): ?>
        <p><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <div class="programme-card">
        <h2>Assign Module to Programme</h2>

        <form method="POST">
            <input type="hidden" name="programme_id" value="<?php echo $programmeId; ?>">
            <input type="hidden" name="action" value="add">

            <label>Module</label><br>
            <select name="module_id" required>
                <option value="">Select Module</option>
                <?php while ($module = $allModules->fetch_assoc()): ?>
                    <option value="<?php echo $module['ModuleID']; ?>">
                        <?php echo htmlspecialchars($module['ModuleName']); ?>
                    </option>
                <?php endwhile; ?>
            </select><br><br>

            <label>Year</label><br>
            <select name="year" required>
                <option value="1">Year 1</option>
                <option value="2">Year 2</option>
                <option value="3">Year 3</option>
            </select><br><br>

            <button type="submit">Assign Module</button>
        </form>
    </div>

    <h2>Current Programme Structure</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>Year</th>
            <th>Module</th>
            <th>Leader</th>
            <th>Action</th>
        </tr>

        <?php while ($row = $assignedResult->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['Year']); ?></td>
                <td><?php echo htmlspecialchars($row['ModuleName']); ?></td>
                <td><?php echo htmlspecialchars($row['ModuleLeader'] ?? ''); ?></td>
                <td>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Remove this module from the programme?');">
                        <input type="hidden" name="programme_id" value="<?php echo $programmeId; ?>">
                        <input type="hidden" name="programme_module_id" value="<?php echo $row['ProgrammeModuleID']; ?>">
                        <input type="hidden" name="action" value="remove">
                        <button type="submit">Remove</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <p><a href="programmes.php">Back to Programmes</a></p>
</div>

</body>
</html>