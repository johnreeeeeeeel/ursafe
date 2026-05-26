<?php
session_start();
require '../db_connection.php';

// Get email from session
$email = $_SESSION['email'] ?? null;

if (!$email) {
    $_SESSION['alert_message'] = [
        'type' => 'danger',
        'text' => 'Something went wrong. Please try again.'
    ];

    header("Location: ../../index.php");
    exit();
}

$current = $_POST['current_password'];
$new = $_POST['new_password'];
$confirm = $_POST['confirm_password'];

if ($new !== $confirm) {
    $_SESSION['field_error']['confirm_password'] = '⚠️ Passwords do not match';

    header("Location: ../../user/profile.php#change_password");
    exit();
}

// Get user by email
$stmt = $conn_local->prepare("CALL getUserByEmail(?)");
$stmt->bind_param("s", $email);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();

$stmt->close();

while ($conn_local->more_results()) {
    $conn_local->next_result();
}

// Verify old password
if ($user && password_verify($current, $user['password'])) {

    $hashedPassword = password_hash($new, PASSWORD_DEFAULT);

    // Update password
    $update = $conn_local->prepare("CALL updateUserPassword(?, ?)");
    $update->bind_param("ss", $email, $hashedPassword);

    $success = $update->execute();

    $update->close();

    while ($conn_local->more_results()) {
        $conn_local->next_result();
    }

    if ($success) {
        $_SESSION['alert_message'] = [
            'type' => 'success',
            'text' => 'Password updated successfully!'
        ];

        $_SESSION['show_success_password_change_modal'] = true;
    
        header("Location: ../../user/profile.php");
        exit();
    } else {
        $_SESSION['alert_message'] = [
            'type' => 'danger',
            'text' => 'Failed to update password.'
        ];

        header("Location: ../../user/profile.php#change_password");
        exit();
    }

} else {
    $_SESSION['field_error']['current_password'] = '⚠️ Incorrect current password';
    
    header("Location: ../../user/profile.php#change_password");
    exit();
}

header("Location: ../../user/profile.php");
exit();
?>