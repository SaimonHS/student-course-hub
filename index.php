<?php
include 'includes/db.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$level = isset($_GET['level']) ? trim($_GET['level']) : '';

if ($level !== 'Undergraduate' && $level !== 'Postgraduate' && $level !== '') {
    $level = '';
}

if ($search !== '' && $level !== '') {

    $sql = "SELECT p.ProgrammeID, p.ProgrammeName, p.Description, l.LevelName
            FROM Programmes p
            JOIN Levels l ON p.LevelID = l.LevelID
            WHERE p.IsPublished = 1
            AND (p.ProgrammeName LIKE ? OR p.Description LIKE ?)
            AND l.LevelName = ?
            ORDER BY p.ProgrammeName ASC";

    $stmt = $conn->prepare($sql);
    $likeSearch = "%" . $search . "%";
    $stmt->bind_param("sss", $likeSearch, $likeSearch, $level);

} elseif ($search !== '') {

    $sql = "SELECT p.ProgrammeID, p.ProgrammeName, p.Description, l.LevelName
            FROM Programmes p
            JOIN Levels l ON p.LevelID = l.LevelID
            WHERE p.IsPublished = 1
            AND (p.ProgrammeName LIKE ? OR p.Description LIKE ?)
            ORDER BY p.ProgrammeName ASC";

    $stmt = $conn->prepare($sql);
    $likeSearch = "%" . $search . "%";
    $stmt->bind_param("ss", $likeSearch, $likeSearch);

} elseif ($level !== '') {

    $sql = "SELECT p.ProgrammeID, p.ProgrammeName, p.Description, l.LevelName
            FROM Programmes p
            JOIN Levels l ON p.LevelID = l.LevelID
            WHERE p.IsPublished = 1
            AND l.LevelName = ?
            ORDER BY p.ProgrammeName ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $level);

} else {

    $sql = "SELECT p.ProgrammeID, p.ProgrammeName, p.Description, l.LevelName
            FROM Programmes p
            JOIN Levels l ON p.LevelID = l.LevelID
            WHERE p.IsPublished = 1
            ORDER BY p.ProgrammeName ASC";

    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();

$queryString = http_build_query([
    'search' => $search,
    'level' => $level
]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome To Student Course Hub</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="page-container">

<div class="top-bar">
    <a href="http://localhost/student-course-hub/admin/login.php" class="admin-login-btn">
        Login as Admin
    </a>
</div>

    <h1>Student Course Hub</h1>
    <p>Explore our available undergraduate and postgraduate programmes.</p>

    <form method="GET" action="index.php" class="search-filter-box">

        <div class="form-row">

            <div class="form-group">
                <label>Search Programmes</label>
                <input 
                    type="text" 
                    name="search" 
                    value="<?php echo htmlspecialchars($search); ?>"
                    placeholder="e.g. Cyber Security"
                >
            </div>

            <div class="form-group">
                <label>Level</label>
                <select name="level">
                    <option value="">All Levels</option>
                    <option value="Undergraduate" <?php echo ($level === 'Undergraduate') ? 'selected' : ''; ?>>
                        Undergraduate
                    </option>
                    <option value="Postgraduate" <?php echo ($level === 'Postgraduate') ? 'selected' : ''; ?>>
                        Postgraduate
                    </option>
                </select>
            </div>

        </div>

        <div class="button-row">
            <button type="submit">Search</button>
            <a href="index.php" class="secondary-button">Clear</a>
        </div>

    </form>

    <p class="results-count">
        <?php echo $result->num_rows; ?> programme(s) found
    </p>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            
            <div class="programme-card">
                <h2><?php echo htmlspecialchars($row['ProgrammeName']); ?></h2>
                <p><strong>Level:</strong> <?php echo htmlspecialchars($row['LevelName']); ?></p>
                <p><?php echo htmlspecialchars($row['Description']); ?></p>

                <a href="programme.php?id=<?php echo $row['ProgrammeID']; ?>&<?php echo $queryString; ?>">
                    View Details
                </a>
            </div>

        <?php endwhile; ?>
    <?php else: ?>
        <div class="programme-card">
            <h2>No programmes found</h2>
            <p>Try another search or filter.</p>
        </div>
    <?php endif; ?>

</div>

</body>
</html>