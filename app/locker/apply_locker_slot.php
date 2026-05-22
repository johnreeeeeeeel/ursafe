<?php
session_start();
require '../db_connection.php';
require '../emails/apply_locker_slot_email.php';

$user_id = $_SESSION['id'] ?? null;
$slot_id = $_POST['slot_id'] ?? null;

try {
    $stmt = $conn_local->prepare("CALL applyLocker(?, ?)");
    $stmt->bind_param("si", $user_id, $slot_id);
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

    $email = $_SESSION['email'];
    $username = $_SESSION['username'];

    // sendLockerSlotApplication(
    //     $email,
    //     $username,
    //     $slot['location'],
    //     $slot['slot_number'],
    //     $slot['size'],
    //     $slot['price']
    // );

    $_SESSION['alert_message'] = [
        'type' => 'success',
        'text' => 'Application submitted successfully!'
    ];

} catch (Exception $e) {
    $_SESSION['alert_message'] = [
        'type' => 'danger',
        'text' => $e->getMessage()
    ];
}

header("Location: ../../user/lockers.php");
exit;
?>