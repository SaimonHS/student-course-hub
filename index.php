<?php
include 'includes/db.php';

$sql = "SELECT p.ProgrammeID, p.ProgrammeName, p.Description, l.LevelName
        FROM Programmes p
        JOIN Levels l ON p.LevelID = l.LevelID";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Course Hub</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<h1>Student Course Hub</h1>
<p>Explore our available programmes</p>

<?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='programme-card'>";
        echo "<h2>" . htmlspecialchars($row['ProgrammeName']) . "</h2>";
        echo "<p><strong>Level:</strong> " . htmlspecialchars($row['LevelName']) . "</p>";
        echo "<p>" . htmlspecialchars($row['Description']) . "</p>";
        echo "<a href='programme.php?id=" . $row['ProgrammeID'] . "'>View Details</a>";
        echo "</div>";
    }
} else {
    echo "No programmes found.";
}
?>

</body>
</html>