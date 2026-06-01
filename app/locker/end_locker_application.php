<!-- Use this file to end locker applications if the hosting does not support events, use this as scheduled task -->
 
<?php
session_start();

require_once __DIR__ . '/../db_connection.php';
require_once __DIR__ . '/../emails/end_locker_slot_email.php';

try {
    $result = $conn_local->query("CALL getEndedAcceptedLockerApplications()");

    while ($result && $app = $result->fetch_assoc()) {

        sendLockerSlotEnded(
            $app['email'],
            $app['username'],
            $app['location'],
            $app['slot_number'],
            $app['size'],
            $app['price']
        );
    }

    while ($conn_local->more_results() && $conn_local->next_result()) {}

    $conn_local->query("CALL endLockerApplication_applicationStatus()");
    while ($conn_local->more_results() && $conn_local->next_result()) {}

    $conn_local->query("CALL endLockerApplication_lockerStatus()");
    while ($conn_local->more_results() && $conn_local->next_result()) {}

} catch (Exception $e) {
    error_log("Scheduler Error: " . $e->getMessage());
}
?>