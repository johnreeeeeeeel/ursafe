<?php
session_start();
require '../db_connection.php';

$id = $_POST['id'];
$location = $_POST['location'];

try {
    $stmt = $conn_local->prepare("CALL updateLockerLocation(?, ?)");
    $stmt->bind_param("is", $id, $location);

    $stmt->execute();

    $_SESSION['alert_message'] = [
        'type' => 'success',
        'text' => 'Location updated successfully.'
    ];

    $stmt->close();
    $conn_local->next_result();

} catch (Exception $e) {
    $_SESSION['alert_message'] = [
        'type' => 'danger',
        'text' => $e->getMessage()
    ];
}

header("Location: ../../admin/lockers.php?tab=lockerLocationsTab");
exit();
?>