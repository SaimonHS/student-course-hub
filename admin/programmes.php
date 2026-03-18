<?php
include 'auth.php';
include '../includes/db.php';

$sql = "SELECT p.ProgrammeID, p.ProgrammeName, p.Description, p.IsPublished,
               l.LevelName, s.Name AS ProgrammeLeader
        FROM Programmes p
        LEFT JOIN Levels l ON p.LevelID = l.LevelID
        LEFT JOIN Staff s ON p.ProgrammeLeaderID = s.StaffID
        ORDER BY p.ProgrammeName ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Programmes</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="page-container">
    <h1>Manage Programmes</h1>

    <p><a href="add_programme.php">Add New Programme</a></p>

    <table border="1" cellpadding="10">
        <tr>
            <th>Name</th>
            <th>Level</th>
            <th>Leader</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['ProgrammeName']); ?></td>
                <td><?php echo htmlspecialchars($row['LevelName']); ?></td>
                <td><?php echo htmlspecialchars($row['ProgrammeLeader'] ?? ''); ?></td>
                <td><?php echo $row['IsPublished'] ? 'Published' : 'Unpublished'; ?></td>
                <td>
                    <a href="edit_programme.php?id=<?php echo $row['ProgrammeID']; ?>">Edit</a> |
                    <a href="manage_programme_modules.php?programme_id=<?php echo $row['ProgrammeID']; ?>">Manage Modules</a> |

                    <form action="toggle_publish.php" method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $row['ProgrammeID']; ?>">
                        <button type="submit">
                            <?php echo $row['IsPublished'] ? 'Unpublish' : 'Publish'; ?>
                        </button>
                    </form>

                    <form action="delete_programme.php" method="POST" style="display:inline;" onsubmit="return confirm('Delete this programme?');">
                        <input type="hidden" name="id" value="<?php echo $row['ProgrammeID']; ?>">
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