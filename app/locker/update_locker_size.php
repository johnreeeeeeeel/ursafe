<?php
session_start();
require '../db_connection.php';

$id = $_POST['id'];
$size = $_POST['size'];
$price = $_POST['price'];

try {
    $stmt = $conn_local->prepare("CALL updateLockerSize(?, ?, ?)");
    $stmt->bind_param("isd", $id, $size, $price);

    $stmt->execute();

    $_SESSION['alert_message'] = [
        'type' => 'success',
        'text' => 'Locker size updated successfully.'
    ];

    $stmt->close();
    $conn_local->next_result();

} catch (Exception $e) {
    $_SESSION['alert_message'] = [
        'type' => 'danger',
        'text' => $e->getMessage()
    ];
}

header("Location: ../../admin/lockers.php#lockerSizesOffcanvas");
exit;
?>