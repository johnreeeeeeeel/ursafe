<?php
session_start();
require '../db_connection.php';

$academic_year = $_POST['academic_year'];
$semester = $_POST['semester'];
$start_at = $_POST['start_at'];
$end_at = $_POST['end_at'];

try {
    $stmt = $conn_local->prepare("CALL addAcademicYear(?, ?, ?, ?)");
    $stmt->bind_param("ssss", $academic_year, $semester, $start_at, $end_at);

    $stmt->execute();

    $_SESSION['alert_message'] = [
        'type' => 'success',
        'text' => 'Academic year added successfully.'
    ];

    $stmt->close();
    $conn_local->next_result();

} catch (Exception $e) {
    $_SESSION['alert_message'] = [
        'type' => 'danger',
        'text' => $e->getMessage()
    ];
}

header("Location: ../../admin/lockers.php#academicCalendarOffcanvas");
exit();
?>