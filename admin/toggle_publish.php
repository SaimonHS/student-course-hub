<?php
include 'auth.php';
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: programmes.php");
    exit();
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($id > 0) {
    $stmt = $conn->prepare("UPDATE Programmes
                            SET IsPublished = CASE WHEN IsPublished = 1 THEN 0 ELSE 1 END
                            WHERE ProgrammeID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: programmes.php");
exit();