<?php
session_start();
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = $_POST['id'];

    $stmt = $conn->prepare("CALL deleteUser(?)");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {

        $_SESSION['alert_message'] = [
            'type' => 'success',
            'text' => 'User deleted successfully!'
        ];

    } else {

        $_SESSION['alert_message'] = [
            'type' => 'danger',
            'text' => 'Error deleting user: ' . $stmt->error
        ];
    }

    header('Location: ../admin/users.php');
    exit();
}
?>