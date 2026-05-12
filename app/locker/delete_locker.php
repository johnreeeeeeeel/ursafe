<?php
session_start();
require '../db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = $_POST['id'];

    $stmt = $conn_local->prepare("CALL deleteLocker(?)");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['alert_message'] = [
            'type' => 'warning',
            'text' => 'Locker slot deleted successfully.'
        ];

    } else {
        $_SESSION['alert_message'] = [
            'type' => 'danger',
            'text' => 'Error deleting locker slot: ' . $stmt->error
        ];
    }

    $stmt->close();
    $conn_local->next_result();

    header("Location: ../../admin/lockers.php");
}
?>