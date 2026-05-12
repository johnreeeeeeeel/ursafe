<?php
session_start();
require '../db_connection.php';
require '../emails/cancel_locker_slot_application_email.php';

$id = $_POST['id'];

// Get application data 
$stmt = $conn_local->prepare("CALL getUserLockerApplicationId(?)");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

$stmt->close();
$conn_local->next_result(); 

$user_id = $data['user_id'];
$slot_id = $data['slot_id'];

// Cancel application
$stmt = $conn_local->prepare("CALL cancelLockerApplication(?)");
$stmt->bind_param("i", $id);
$stmt->execute();

$stmt->close();
$conn_local->next_result(); 

// Get slot details
$stmt = $conn_local->prepare("CALL getUserLockerApplicationDetails(?)");
$stmt->bind_param("i", $slot_id);
$stmt->execute();
$result = $stmt->get_result();
$slot = $result->fetch_assoc();

$stmt->close();
$conn_local->next_result(); 

$email = $_SESSION['email'];
$username = $_SESSION['username'];

sendLockerSlotCancellation(
    $email,
    $username,
    $slot['location'],
    $slot['slot_number'],
    $slot['size'],
    $slot['price']
);

$_SESSION['alert_message'] = [
    'type' => 'warning',
    'text' => 'Application cancelled successfully!'
];

header("Location: ../../user/lockers.php#myLockerApplicationOffcanvas");
exit;
?>