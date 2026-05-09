<?php
session_start();
require '../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['loginEmail']);
    $password = trim($_POST['loginPassword']);

    $user = null;
    
    $isAdmin = false;
    $isUser = false;

    // Check admin
    if (!$user) {
        $stmt = $conn_local->prepare("CALL getAdminByEmail(?)");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $isAdmin = true;
        }

        $stmt->close();
        $conn_local->next_result();
    }

    // Check user 
    if (!$user) {
        $stmt = $conn_local->prepare("CALL getUserByEmail(?)");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $isUser = true;
        }

        $stmt->close();
        $conn_local->next_result();
    }

    // If still no user found
    if (!$user) {
        $_SESSION['alert_message'] = [
            'type' => 'danger',
            'text' => 'Account not found'
        ];
        header("Location: ../../index.php");
        exit;
    }

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

        if ($isAdmin) {
            $_SESSION['role'] = 'admin';
            header("Location: ../../admin/dashboard.php");

        } elseif ($isUser) {
            $_SESSION['role'] = 'user';
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
        $_SESSION['alert_message'] = [
            'type' => 'danger',
            'text' => 'Invalid email or password'
        ];
        header("Location: ../../index.php");
        exit;
    }
}
?>