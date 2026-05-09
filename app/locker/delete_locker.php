<?php
require '../db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = $_POST['id'];

    $stmt = $conn_local->prepare("CALL deleteLockerSlot(?)");
    $stmt->bind_param("i", $id);

    $stmt->execute();

    $stmt->close();
    $conn_local->next_result();

    header("Location: ../../admin/lockers.php");
}
?>