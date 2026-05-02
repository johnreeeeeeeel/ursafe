<?php
session_start();
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id         = $_POST['id'];
    $firstname  = $_POST['firstname'];
    $lastname   = $_POST['lastname'];
    $middlename = $_POST['middlename'] ?? null;
    $sex        = $_POST['sex'] ?? null;
    $dob        = $_POST['dob'] ?? null;
    $institute  = $_POST['institute'] ?? null;
    $program    = $_POST['program'] ?? null;
    $email      = $_POST['email'];

    $stmt = $conn->prepare("CALL updateUser(?,?,?,?,?,?,?,?,?)");

    $stmt->bind_param(
        "issssssss",
        $id,
        $firstname,
        $lastname,
        $middlename,
        $sex,
        $dob,
        $institute,
        $program,
        $email
    );

    if ($stmt->execute()) {
        $_SESSION['alert_message'] = [
            'type' => 'success',
            'text' => 'User updated successfully!'
        ];
    } else {
        $_SESSION['alert_message'] = [
            'type' => 'danger',
            'text' => 'Update failed!'
        ];
    }

    header('Location: ../admin/users.php');
    exit();
}
?>