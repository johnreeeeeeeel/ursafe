<?php
session_start();
require '../db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $slot_number = $_POST['slot_number'];
    $location_id = $_POST['location_id'];
    $size_id = $_POST['size_id'];
    $start_at = $_POST['start_at'];
    $end_at = $_POST['end_at'];

    $stmt = $conn_local->prepare("CALL addLocker(?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiss", $slot_number, $location_id, $size_id, $start_at, $end_at);

    if ($stmt->execute()) {
        $_SESSION['alert_message'] = [
            'type' => 'success',
            'text' => 'Locker slot added successfully.'
        ];

    } else {
        $_SESSION['alert_message'] = [
            'type' => 'danger',
            'text' => 'Error adding locker slot: ' . $stmt->error
        ];
    }

    $stmt->close();
    $conn_local->next_result(); 

    header("Location: ../../admin/lockers.php");
    exit();
}
?>