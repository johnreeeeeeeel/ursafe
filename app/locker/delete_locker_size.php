<?php
session_start();
require '../db_connection.php';

$id = $_POST['id'];

$stmt = $conn_local->prepare("CALL deleteLockerSize(?)");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['alert_message'] = [
        'type' => 'warning',
        'text' => 'Locker size deleted successfully.'
    ];
} else {
    $_SESSION['alert_message'] = [
        'type' => 'danger',
        'text' => 'Failed to delete locker size.'
    ];
}

$stmt->close();
$conn_local->next_result();

header("Location: ../../admin/lockers.php#lockerSizesOffcanvas");
exit;
?>