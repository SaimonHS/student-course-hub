<?php
include 'auth.php';
include '../includes/db.php';

$sql = "SELECT 
        i.StudentName,
        i.Email,
        i.RegisteredAt,
        p.ProgrammeName
        FROM InterestedStudents i
        JOIN Programmes p ON i.ProgrammeID = p.ProgrammeID
        ORDER BY i.RegisteredAt DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interested Students</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="page-container">
    <h1>Interested Students Mailing List</h1>

    <table border="1" cellpadding="10">
        <tr>
            <th>Programme</th>
            <th>Student Name</th>
            <th>Email</th>
            <th>Registered Date</th>
        </tr>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['ProgrammeName']); ?></td>
                    <td><?php echo htmlspecialchars($row['StudentName']); ?></td>
                    <td><?php echo htmlspecialchars($row['Email']); ?></td>
                    <td><?php echo htmlspecialchars($row['RegisteredAt']); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
    </table>

    <p><a href="dashboard.php">Back to Dashboard</a></p>
</div>

</body>
</html>