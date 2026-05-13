<?php
session_start();
require '../db_connection.php';
require '../emails/reject_locker_slot_email.php';

$app_id = $_POST['id'] ?? null;

if (!$app_id) {
    header("Location: ../../admin/lockers.php");
    exit;
}

try {
    $stmt = $conn_local->prepare("CALL getUserLockerApplicationId(?)");
    $stmt->bind_param("i", $app_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $app = $result->fetch_assoc();

    $stmt->close();
    $conn_local->next_result();

    if (!$app) {
        throw new Exception("Application not found.");
    }

    $slot_id = $app['slot_id'];
    $user_id = $app['user_id'];

    $stmt = $conn_local->prepare("CALL rejectLockerApplication(?)");
    $stmt->bind_param("i", $app_id);
    $stmt->execute();
    $stmt->close();
    $conn_local->next_result();

    $stmt = $conn_local->prepare("CALL getUserLockerApplicationDetails(?)");
    $stmt->bind_param("i", $slot_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $slot = $result->fetch_assoc();

    $stmt->close();
    $conn_local->next_result();

    $stmt = $conn_local->prepare("CALL getUserById(?)");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $stmt->close();
    $conn_local->next_result();

    sendLockerSlotRejected(
        $user['email'],
        $user['username'],
        $slot['location'],
        $slot['slot_number'],
        $slot['size'],
        $slot['price']
    );

    $_SESSION['alert_message'] = [
        'type' => 'warning',
        'text' => 'Application rejected successfully.'
    ];

} catch (Exception $e) {

    $_SESSION['alert_message'] = [
        'type' => 'danger',
        'text' => $e->getMessage()
    ];
}

header("Location: ../../admin/lockers.php#lockerPendingApplicationOffcanvas");
exit;
?>