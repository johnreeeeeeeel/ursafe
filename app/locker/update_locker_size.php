<?php
session_start();
require '../db_connection.php';

$id = $_POST['id'];
$size = $_POST['size'];
$price = $_POST['price'];

$stmt = $conn_local->prepare("CALL updateLockerSize(?, ?, ?)");
$stmt->bind_param("isd", $id, $size, $price);

if ($stmt->execute()) {
    $_SESSION['alert_message'] = [
        'type' => 'success',
        'text' => 'Locker size updated successfully.'
    ];
} else {
    $_SESSION['alert_message'] = [
        'type' => 'danger',
        'text' => 'Failed to update locker size.'
    ];
}

$stmt->close();
$conn_local->next_result();

header("Location: ../../admin/lockers.php");
exit;
?>