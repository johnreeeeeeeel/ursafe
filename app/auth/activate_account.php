<?php
require '../db_connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'];
    $username = $_POST['username'];
    $plainPassword = $_POST['password'];
    $password = password_hash($plainPassword, PASSWORD_DEFAULT);

    try {
        // Check and validate user account
        $stmt = $conn_local->prepare("CALL validateAndActivateUserAccount(?, ?, ?)");
        $stmt->bind_param("sss", $email, $username, $password);
        $stmt->execute();

        $result = $stmt->get_result();
        $student = $result->fetch_assoc();

        $stmt->close();

        require '../emails/success_account_activation_email.php';

        sendUserEmail(
            $student['email'],
            $username,
            $plainPassword
        );

        $_SESSION['alert_message'] = [
            'type' => 'success',
            'text' => 'Account created and activated successfully'
        ];

    } catch (mysqli_sql_exception $e) {

        $_SESSION['alert_message'] = [
            'type' => 'danger',
            'text' => $e->getMessage()
        ];

        header("Location: ../../index.php");
        exit;
    }

    header("Location: ../../index.php");
    exit;
}
?>