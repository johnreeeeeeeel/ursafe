<?php
require '../db_connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['accountActivationEmail'];
    $username = $_POST['accountActivationUsername'];
    $password = $_POST['accountActivationPassword'];
    $confirmPassword = $_POST['accountActivationConfirmPassword'];

    $_SESSION['account_activation_email'] = $email;
    $_SESSION['account_activation_username'] = $username;

    // Check passowrd
    if ($password !== $confirmPassword) {
        $_SESSION['field_error']['confirm_password'] = '⚠️ Passwords do not match';

        header("Location: ../../index.php#activateAccount");
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Validate and activate account
        $stmt = $conn_local->prepare("CALL validateAndActivateUserAccount(?, ?, ?)");
        $stmt->bind_param("sss", $email, $username, $hashedPassword);
        $stmt->execute();

        $result = $stmt->get_result();
        $student = $result->fetch_assoc();

        $stmt->close();

        while ($conn_local->more_results()) {
            $conn_local->next_result();
        }

        require '../emails/success_account_activation_email.php';

        sendUserEmail(
            $student['email'],
            $username,
            $password
        );

        unset(
            $_SESSION['account_activation_email'],
            $_SESSION['account_activation_username']
        );

        $_SESSION['alert_message'] = [
            'type' => 'success',
            'text' => 'Account activated successfully'
        ];

    } catch (mysqli_sql_exception $e) {
        switch ($e->getMessage()) {

            case 'EMAIL_NOT_FOUND':
                $_SESSION['field_error']['email'] = '⚠️ Please use your campus email';
                break;

            case 'ACCOUNT_ALREADY_ACTIVE':
                $_SESSION['alert_message'] = [
                    'type' => 'danger',
                    'text' => 'Account already activated. Please login instead'
                ];
                break;

            case 'USERNAME_EXISTS':
                $_SESSION['field_error']['username'] = '⚠️ Username already exists';
                break;

            default:
                $_SESSION['alert_message'] = [
                    'type' => 'danger',
                    'text' => $e->getMessage()
                ];
        }

        header("Location: ../../index.php#activateAccount");
        exit;
    }

    unset(
        $_SESSION['account_activation_email'],
        $_SESSION['account_activation_username'],
        $_SESSION['account_activation_password'],
        $_SESSION['account_activation_confirm_password']
    );

    $_SESSION['show_success_account_activation_modal'] = true;

    header("Location: ../../index.php");
    exit;
}
?>