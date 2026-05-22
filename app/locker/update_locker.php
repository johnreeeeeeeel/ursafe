<?php
session_start();
require '../db_connection.php';

$id = $_POST['id'];
$slot_number = $_POST['slot_number'];
$start_at = $_POST['start_at'];
$end_at = $_POST['end_at'];
$status = $_POST['status'];

try {
    $stmt = $conn_local->prepare("CALL updateLocker(?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $id, $slot_number, $start_at, $end_at, $status);

    $stmt->execute();

    $_SESSION['alert_message'] = [
        'type' => 'success',
        'text' => 'Locker slot updated successfully.'
    ];

    $stmt->close();
    $conn_local->next_result();
    
} catch (Exception $e) {
    $_SESSION['alert_message'] = [
        'type' => 'danger',
        'text' => $e->getMessage()
    ];
}

header("Location: ../../admin/lockers.php");
exit();
?>