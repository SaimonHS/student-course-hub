<?php
include 'includes/db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Programme ID is missing.");
}

$programmeId = (int) $_GET['id'];

$backSearch = isset($_GET['search']) ? trim($_GET['search']) : '';
$backLevel = isset($_GET['level']) ? trim($_GET['level']) : '';

$backQuery = http_build_query([
    'search' => $backSearch,
    'level' => $backLevel
]);

$sqlProgramme = "SELECT p.ProgrammeID, p.ProgrammeName, p.Description, l.LevelName, s.Name AS ProgrammeLeader
                 FROM Programmes p
                 JOIN Levels l ON p.LevelID = l.LevelID
                 LEFT JOIN Staff s ON p.ProgrammeLeaderID = s.StaffID
                 WHERE p.ProgrammeID = ?";

$stmtProgramme = $conn->prepare($sqlProgramme);
$stmtProgramme->bind_param("i", $programmeId);
$stmtProgramme->execute();
$resultProgramme = $stmtProgramme->get_result();

if ($resultProgramme->num_rows === 0) {
    die("Programme not found.");
}

$programme = $resultProgramme->fetch_assoc();

$sqlModules = "SELECT pm.Year, m.ModuleName, m.Description, s.Name AS ModuleLeader
               FROM ProgrammeModules pm
               JOIN Modules m ON pm.ModuleID = m.ModuleID
               LEFT JOIN Staff s ON m.ModuleLeaderID = s.StaffID
               WHERE pm.ProgrammeID = ?
               ORDER BY pm.Year, m.ModuleName";

$stmtModules = $conn->prepare($sqlModules);
$stmtModules->bind_param("i", $programmeId);
$stmtModules->execute();
$resultModules = $stmtModules->get_result();

$modulesByYear = [];

while ($row = $resultModules->fetch_assoc()) {
    $year = $row['Year'];
    $modulesByYear[$year][] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($programme['ProgrammeName']); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<h1><?php echo htmlspecialchars($programme['ProgrammeName']); ?></h1>

<p><strong>Level:</strong> <?php echo htmlspecialchars($programme['LevelName']); ?></p>
<p><strong>Programme Leader:</strong> <?php echo htmlspecialchars($programme['ProgrammeLeader']); ?></p>
<p><?php echo htmlspecialchars($programme['Description']); ?></p>

<h2>Modules by Year</h2>

<?php if (!empty($modulesByYear)): ?>
    <?php foreach ($modulesByYear as $year => $modules): ?>
        <div class="programme-card">
            <h3>Year <?php echo htmlspecialchars($year); ?></h3>

            <?php foreach ($modules as $module): ?>
                <div style="margin-bottom: 15px;">
                    <h4><?php echo htmlspecialchars($module['ModuleName']); ?></h4>
                    <p><?php echo htmlspecialchars($module['Description']); ?></p>
                    <p><strong>Module Leader:</strong> <?php echo htmlspecialchars($module['ModuleLeader']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No modules found for this programme.</p>
<?php endif; ?>

<h2>Register Your Interest</h2>

<form action="register_interest.php" method="POST" class="programme-card">
    <input type="hidden" name="programme_id" value="<?php echo $programme['ProgrammeID']; ?>">

    <label for="student_name">Your Name</label><br>
    <input type="text" id="student_name" name="student_name" required><br><br>

    <label for="email">Email Address</label><br>
    <input type="email" id="email" name="email" required><br><br>

    <button type="submit">Submit</button>
</form>

<!-- <p><a href="index.php">← Back to Programmes</a></p> -->
<p><a href="index.php<?php echo !empty($backQuery) ? '?' . $backQuery : ''; ?>">← Back to Programmes</a></p>

</body>
</html>