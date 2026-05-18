<?php
session_start();
require '../db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = $_POST['id'];
    $slot_number = $_POST['slot_number'];
    $start_at = $_POST['start_at'];
    $end_at = $_POST['end_at'];
    $status = $_POST['status'];

    $stmt = $conn_local->prepare("CALL updateLocker(?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $id, $slot_number, $start_at, $end_at, $status);

    if ($stmt->execute()) {
        $_SESSION['alert_message'] = [
            'type' => 'success',
            'text' => 'Locker slot updated successfully.'
        ];
    } else {
        $_SESSION['alert_message'] = [
            'type' => 'danger',
            'text' => 'Error updating locker slot: ' . $stmt->error
        ];
    }

    $stmt->close();
    $conn_local->next_result();

    header("Location: ../../admin/lockers.php");
    exit();
}
?>