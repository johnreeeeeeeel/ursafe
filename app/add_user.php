<?php
session_start();
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Data
    $firstname  = trim($_POST['firstname']);
    $lastname   = trim($_POST['lastname']);
    $middlename = !empty($_POST['middlename']) ? $_POST['middlename'] : null;
    $sex        = !empty($_POST['sex']) ? $_POST['sex'] : null;
    $dob        = !empty($_POST['dob']) ? $_POST['dob'] : null;

    $usertype_id  = $_POST['userType'];

    $institute_id = !empty($_POST['institute']) ? $_POST['institute'] : null;
    $program_id   = !empty($_POST['program']) ? $_POST['program'] : null;

    $email   = trim($_POST['email']);
    $status = !empty($_POST['status']) ? $_POST['status'] : 'Inactive';


    $stmt = $conn->prepare("CALL addUser(?,?,?,?,?,?,?,?,?,?)");

    $stmt->bind_param(
        "ssssssisss",
        $firstname,
        $lastname,
        $middlename,
        $sex,
        $dob,
        $email,
        $usertype_id,
        $institute_id,
        $program_id,
        $status
    );

    if ($stmt->execute()) {
        // Alert message
        $_SESSION['alert_message'] = [
            'type' => 'success',
            'text' => 'User created successfully!'
        ];

        header('Location: ../admin/admin.php');
        exit();

    } else {
        // Alert message
        $_SESSION['alert_message'] = [
            'type' => 'danger',
            'text' => 'Error inserting user: ' . $stmt->error
        ];

        header('Location: ../admin/admin.php');
        exit();
    }
}
?>