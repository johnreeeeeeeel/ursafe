<?php
require '../db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = $_POST['id'];
    $slot_number = $_POST['slot_number'];
    $status = $_POST['status'];

    $stmt = $conn_local->prepare("CALL updateLockerSlot(?, ?, ?)");
    $stmt->bind_param("iis", $id, $slot_number, $status);

    if ($stmt->execute()) {
        $_SESSION['alert_message'] = [
            'type' => 'success',
            'text' => 'Locker slot updated successfully.'
        ];
    } else {
        $_SESSION['alert_message'] = [
            'type' => 'danger',
            'text' => 'Error updating locker slot.'
        ];
    }

    $stmt->close();
    $conn_local->next_result();

    header("Location: ../../admin/lockers.php");
    exit();
}
?>