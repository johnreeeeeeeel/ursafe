<?php
session_start();
require '../db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $slot_number = $_POST['slot_number'];
    $location_id = $_POST['location_id'];
    $size_id = $_POST['size_id'];

    $stmt = $conn_local->prepare("CALL addLockerSlot(?, ?, ?)");
    $stmt->bind_param("iii", $slot_number, $location_id, $size_id);

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