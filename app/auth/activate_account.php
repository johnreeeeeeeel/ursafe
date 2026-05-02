<?php
require '../db_connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'];
    $username = $_POST['username'];
    $plainPassword = $_POST['password']; // ✔️ FIX: define this

    // 1. Check if email exists
    $stmt = $conn->prepare("SELECT id, status FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['alert_message'] = [
            'type' => 'danger',
            'text' => 'Email not found'
        ];
        header("Location: ../../index.php");
        exit;
    }

    $user = $result->fetch_assoc();

    // 2. Check if already active
    if ($user['status'] === 'Active') {
        $_SESSION['alert_message'] = [
            'type' => 'warning',
            'text' => 'Account is already active'
        ];
        header("Location: ../../index.php");
        exit;
    }

    // 3. Hash password
    $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

    // 4. Update user account
    $update = $conn->prepare("
        UPDATE users 
        SET username = ?, password = ?, status = 'Active'
        WHERE email = ?
    ");

    $update->bind_param("sss", $username, $hashedPassword, $email);

    if ($update->execute()) {

        // ✅ Send email with plain password (NOT hashed)
        require '../emails/success_creation_email.php';
        sendUserEmail($email, $username, $plainPassword);

        $_SESSION['alert_message'] = [
            'type' => 'success',
            'text' => 'Account activated successfully. You can now login.'
        ];

    } else {
        $_SESSION['alert_message'] = [
            'type' => 'danger',
            'text' => 'Failed to activate account'
        ];
    }

    header("Location: ../../index.php");
    exit;
}
?>