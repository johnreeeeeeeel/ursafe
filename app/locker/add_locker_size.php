<?php
session_start();
require '../db_connection.php';

$size = $_POST['size'];
$price = $_POST['price'];

try {
    $stmt = $conn_local->prepare("CALL addLockerSize(?, ?)");
    $stmt->bind_param("sd", $size, $price);

    $stmt->execute();

    $_SESSION['alert_message'] = [
        'type' => 'success',
        'text' => 'Locker size added successfully.'
    ];

    $stmt->close();
    $conn_local->next_result();

} catch (Exception $e) {
    $_SESSION['alert_message'] = [
        'type' => 'danger',
        'text' => $e->getMessage()
    ];
}

header("Location: ../../admin/lockers.php?tab=lockerSizesTab");
exit();
?>