<?php
include 'auth.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="page-container">
    <div class="programme-card">
        <h1>Admin Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</p>

        <p><a href="students.php">View Interested Students</a></p>
        <p><a href="programmes.php">Manage Programmes</a></p>
        <p><a href="modules.php">Manage Modules</a></p>
        <p><a href="logout.php">Logout</a></p>
    </div>
</div>

</body>
</html>