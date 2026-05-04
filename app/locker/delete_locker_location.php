<?php
session_start();
require '../db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = $_POST['id'];

    $stmt = $conn_local->prepare("CALL deleteLockerLocation(?)");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['alert_message'] = [
            'type' => 'success',
            'text' => 'Location deleted successfully.'
        ];
    } else {
        $_SESSION['alert_message'] = [
            'type' => 'danger',
            'text' => 'Error deleting location.'
        ];
    }

    $stmt->close();
    $conn_local->next_result();

    header("Location: ../../admin/lockers.php#lockerLocationsOffcanvas");
    exit();
}
?>