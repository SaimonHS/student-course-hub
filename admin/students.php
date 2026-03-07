<?php
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
<html>
<head>
    <title>Interested Students</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<h1>Interested Students Mailing List</h1>

<table border="1" cellpadding="10">
    <tr>
        <th>Programme</th>
        <th>Student Name</th>
        <th>Email</th>
        <th>Registered Date</th>
    </tr>

<?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['ProgrammeName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['StudentName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['RegisteredAt']) . "</td>";
        echo "</tr>";
    }
}
?>

</table>

<p><a href="../index.php">Back to Homepage</a></p>

</body>
</html>