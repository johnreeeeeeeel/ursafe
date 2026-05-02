<?php
session_start();
require 'db_connection.php';

if (!isset($_SESSION['id'])) {
    header('Location: ../index.php');
    exit();
}

$userId = $_SESSION['id'];

$old = $_POST['old_password'];
$new = $_POST['new_password'];
$confirm = $_POST['confirm_password'];

if ($new !== $confirm) {
    $_SESSION['alert_message'] = [
        'type' => 'danger',
        'text' => 'Passwords do not match.'
    ];
} else {

    $stmt = $conn_local->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($old, $user['password'])) {

        $hashed = password_hash($new, PASSWORD_DEFAULT);

        $update = $conn_local->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update->bind_param("si", $hashed, $userId);
        $update->execute();

        $_SESSION['alert_message'] = [
            'type' => 'success',
            'text' => 'Password updated successfully!'
        ];

    } else {
        $_SESSION['alert_message'] = [
            'type' => 'danger',
            'text' => 'Incorrect current password.'
        ];
    }
}

header("Location: ../user/profile.php");
exit();
?>