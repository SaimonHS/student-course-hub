<?php
include 'auth.php';
include '../includes/db.php';

$sql = "SELECT m.ModuleID, m.ModuleName, m.Description, s.Name AS ModuleLeader
        FROM Modules m
        LEFT JOIN Staff s ON m.ModuleLeaderID = s.StaffID
        ORDER BY m.ModuleName ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Modules</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="page-container">
    <h1>Manage Modules</h1>

    <p><a href="add_module.php">Add New Module</a></p>

    <table border="1" cellpadding="10">
        <tr>
            <th>Module Name</th>
            <th>Module Leader</th>
            <th>Actions</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['ModuleName']); ?></td>
                <td><?php echo htmlspecialchars($row['ModuleLeader'] ?? ''); ?></td>
                <td>
                    <a href="edit_module.php?id=<?php echo $row['ModuleID']; ?>">Edit</a>

                    <form action="delete_module.php" method="POST" style="display:inline;" onsubmit="return confirm('Delete this module?');">
                        <input type="hidden" name="id" value="<?php echo $row['ModuleID']; ?>">
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <p><a href="dashboard.php">Back to Dashboard</a></p>
</div>

</body>
</html>