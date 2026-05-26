<?php
require '../db_connection.php';
session_start();

$email = $_POST['loginEmail'];
$password = $_POST['loginPassword'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['loginEmail']);
    $password = trim($_POST['loginPassword']);

    try {
        $stmt = $conn_local->prepare("CALL validateLogin(?)");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $stmt->close();

        // Check password
        if (password_verify($password, $user['password'])) {

            $_SESSION['id']         = $user['id'];

            $_SESSION['lastname']   = $user['lastname'] ?? '';
            $_SESSION['firstname']  = $user['firstname'] ?? '';
            $_SESSION['middlename'] = $user['middlename'] ?? '';

            $_SESSION['sex']        = $user['sex'] ?? '';
            $_SESSION['dob']        = $user['dob'] ?? '';

            $_SESSION['institute']  = $user['institute'] ?? '';
            $_SESSION['program']    = $user['program'] ?? '';

            $_SESSION['username']   = $user['username'];
            $_SESSION['email']      = $user['email'];

            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: ../../admin/dashboard.php");

            } elseif ($user['role'] === 'user') {
                header("Location: ../../user/home.php");

            } else {
                $_SESSION['alert_message'] = [
                    'type' => 'danger',
                    'text' => 'Something went wrong. Please try again.'
                ];

                header("Location: ../../index.php");
            }
            
            exit;

        } else {
            $_SESSION['login_email'] = $email;

            $_SESSION['field_error']['email_or_password'] = '⚠️ Invalid email or password';

            header("Location: ../../index.php");
            exit;
        }

    } catch (mysqli_sql_exception $e) {
        $_SESSION['alert_message'] = [
            'type' => 'danger',
            'text' => $e->getMessage()
        ];

        header("Location: ../../index.php");
        exit;
    }
}
?>