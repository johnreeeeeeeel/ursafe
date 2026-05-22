<?php
session_start();
require '../db_connection.php';

$location = $_POST['location'];

try {
    $stmt = $conn_local->prepare("CALL addLockerLocation(?)");
    $stmt->bind_param("s", $location);

    $stmt->execute();

    $_SESSION['alert_message'] = [
        'type' => 'success',
        'text' => 'Location added successfully.'
    ];

    $stmt->close();
    $conn_local->next_result();

} catch (Exception $e) {
    $_SESSION['alert_message'] = [
        'type' => 'danger',
        'text' => $e->getMessage()
    ];
}

header("Location: ../../admin/lockers.php#lockerLocationsOffcanvas");
exit();
?>