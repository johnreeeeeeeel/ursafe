<?php
session_start();
require '../db_connection.php';

if (!isset($_POST['id'])) {
    exit;
}

$id = $_POST['id'];

// Only allow cancel if still pending
$stmt = $conn_local->prepare("
    UPDATE locker_applications 
    SET status = 'Cancelled'
    WHERE id = ? AND status = 'Pending'
");

$stmt->bind_param("i", $id);
$stmt->execute();

$_SESSION['alert_message'] = [
    'type' => 'success',
    'text' => 'Application cancelled successfully!'
];

header("Location: ../../user/lockers.php#myLockerApplicationOffcanvas");
exit;
?>