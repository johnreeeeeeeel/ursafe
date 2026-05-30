<?php
session_start();
require '../db_connection.php';

$id = $_POST['id'];
$slot_number = $_POST['slot_number'];
$size_id = $_POST['size_id'];
$academic_year_id = $_POST['academic_year_id']; 
$status = $_POST['status'];

try {

    $stmt = $conn_local->prepare("CALL updateLocker(?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "iiiis",
        $id,
        $slot_number,
        $size_id,
        $academic_year_id,
        $status
    );

    $stmt->execute();

    $_SESSION['alert_message'] = [
        'type' => 'success',
        'text' => 'Locker slot updated successfully.'
    ];

    $stmt->close();
    $conn_local->next_result();

} catch (Exception $e) {
    $_SESSION['alert_message'] = [
        'type' => 'danger',
        'text' => $e->getMessage()
    ];
}

header("Location: ../../admin/lockers.php?tab=lockerSlotsTab");
exit();
?>