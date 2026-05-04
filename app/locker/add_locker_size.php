<?php
session_start();
require '../db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $size = $_POST['size'];
    $price = $_POST['price'];

    $stmt = $conn_local->prepare("CALL addLockerSize(?, ?)");
    $stmt->bind_param("sd", $size, $price);

    if ($stmt->execute()) {
        $_SESSION['alert_message'] = [
            'type' => 'success',
            'text' => 'Locker size added successfully.'
        ];
    } else {
        $_SESSION['alert_message'] = [
            'type' => 'danger',
            'text' => 'Error adding locker size.'
        ];
    }

    $stmt->close();
    $conn_local->next_result();

    header("Location: ../../admin/lockers.php#lockerSizesOffcanvas");
    exit();
}
?>