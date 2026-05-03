<?php
session_start();
require 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $location = $_POST['location'];

    $stmt = $conn_local->prepare("CALL addLockerLocation(?)");
    $stmt->bind_param("s", $location);

    if ($stmt->execute()) {
        $_SESSION['alert_message'] = [
            'type' => 'success',
            'text' => 'Location added successfully.'
        ];
    } else {
        $_SESSION['alert_message'] = [
            'type' => 'danger',
            'text' => 'Error adding location.'
        ];
    }

    $stmt->close();
    $conn_local->next_result();

    header("Location: ../admin/lockers.php");
    exit();
}
?>