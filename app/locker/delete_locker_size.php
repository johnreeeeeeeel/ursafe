<?php
session_start();
require '../db_connection.php';

$id = $_POST['id'];

try {
    $stmt = $conn_local->prepare("CALL deleteLockerSize(?)");
    $stmt->bind_param("i", $id);

    $stmt->execute();

    $_SESSION['alert_message'] = [
        'type' => 'warning',
        'text' => 'Locker size deleted successfully.'
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