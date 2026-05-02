<?php
require '../db_connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'];
    $username = $_POST['username'];
    $plainPassword = $_POST['password'];

    // Checkly check if email exists in campus database
    $stmt = $conn_remote->prepare("
        SELECT 
            student_id,
            lastname,
            firstname,
            middlename,
            sex,
            dob,
            institute_id,
            program_id,
            email
        FROM students
        WHERE email = ?
    ");

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['alert_message'] = [
            'type' => 'danger',
            'text' => 'Please use your campus email to activate your account'
        ];
        header("Location: ../../index.php");
        exit;
    }

    $student = $result->fetch_assoc();
    $stmt->close();

    // Check if account already exists in local database
    $stmt = $conn_local->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $check = $stmt->get_result();

    if ($check->num_rows > 0) {
        $_SESSION['alert_message'] = [
            'type' => 'warning',
            'text' => 'Account already activated. Please log in instead.'
        ];
        header("Location: ../../index.php");
        exit;
    }

    $stmt->close();

    // Insert into local database
    $password = password_hash($plainPassword, PASSWORD_DEFAULT);

    $insert = $conn_local->prepare("
        INSERT INTO users (
            id,
            lastname,
            firstname,
            middlename,
            sex,
            dob,
            institute,
            program,
            username,
            email,
            password
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $insert->bind_param(
        "sssssssssss",
        $student['student_id'],
        $student['lastname'],
        $student['firstname'],
        $student['middlename'],
        $student['sex'],
        $student['dob'],
        $student['institute_id'],
        $student['program_id'],
        $username,
        $student['email'],
        $password
    );

    if ($insert->execute()) {

        require '../emails/success_creation_email.php';
        sendUserEmail($student['email'], $username, $plainPassword);

        $_SESSION['alert_message'] = [
            'type' => 'success',
            'text' => 'Account created and activated successfully'
        ];

    } else {
        $_SESSION['alert_message'] = [
            'type' => 'danger',
            'text' => 'Failed to create account'
        ];
    }

    header("Location: ../../index.php");
    exit;
}
?>