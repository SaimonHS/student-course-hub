<?php
include 'auth.php';
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: programmes.php");
    exit();
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($id > 0) {
    $stmt1 = $conn->prepare("DELETE FROM ProgrammeModules WHERE ProgrammeID = ?");
    $stmt1->bind_param("i", $id);
    $stmt1->execute();

    $stmt2 = $conn->prepare("DELETE FROM Programmes WHERE ProgrammeID = ?");
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
}

header("Location: programmes.php");
exit();