<?php
session_start();
require '../app/db_connection.php';

if (!isset($_SESSION['usertype']) || ($_SESSION['usertype'] != 2 && $_SESSION['usertype'] != 3)) {
    header("Location: ../index.php");
    exit;
}

// Get user details
$id = $_SESSION['id'] ?? '';

$lastname = $_SESSION['lastname'] ?? '';
$firstname = $_SESSION['firstname'] ?? '';
$middlename = $_SESSION['middlename'] ?? '';

$sex = $_SESSION['sex'] ?? '';
$dob = $_SESSION['dob'] ?? '';

$usertype = $_SESSION['usertype'] ?? '';
$userrole = $_SESSION['userrole'] ?? '';
$institute = $_SESSION['institute'] ?? '';
$program = $_SESSION['program'] ?? '';

$username = $_SESSION['username'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>DNSC Findr</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Changa+One:ital@0;1&family=Outfit:wght@100..900&display=swap"
        rel="stylesheet">

    <!-- Favicon -->
    <link rel="shortcut icon" href="../assets/images/favicon.ico" type="image/x-icon">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="../assets/css/components.css">
    <link rel="stylesheet" href="../assets/css/user.css">
    <link rel="stylesheet" href="../assets/css/sidebar.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
</head>

<body class="container-fluid">
    <!-- Loading screen -->
    <div id="loadingScreen" class="loading-screen d-none">
        <div class="spinner-grow"></div>
    </div>
    
    <!-- Alert messege -->
    <?php if (isset($_SESSION['alert_message'])): ?>
        <div class="alert alert-<?= $_SESSION['alert_message']['type'] ?> alert-dismissible fade show" id="messageAlert">
            <?= $_SESSION['alert_message']['text'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <?php unset($_SESSION['alert_message']); ?>
    <?php endif; ?>

    <!-- Mobile Sidebar -->
    <div class="offcanvas offcanvas-start" id="sidebarMobile">

        <div class="offcanvas-body">
            <ul class="nav nav-tabs flex-column">

                <a href="user.php">
                    <img class="logo" src="../assets/images/dnscfindr-logo.png" alt="logo">
                </a>

                <div class="top-nav">
                    <li class="nav-item">
                        <a href="#" class="nav-link back-btn" onclick="history.back(); return false;">
                            <i class="fa-solid fa-angle-left"></i>
                            Back
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#changePassword">
                            <i class="fa-solid fa-key"></i>
                            Change Password
                        </a>
                    </li>
                </div>

                <div class="bottom-nav">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#create-post">
                            <i class="fa-solid fa-pen-clip"></i>
                            Create Post
                        </a>
                    </li>

                    <li class="user-profile dropup">
                        <a href="" class="profile" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-circle-user"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="profile.php">
                                    <i class="fa-solid fa-circle-user"></i>
                                    Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="fa-solid fa-gear"></i>
                                    Settings
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="../app/logout.php">
                                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                    logout
                                </a>
                            </li>
                        </ul>
                        <small class="name">
                            <?php
                                echo $firstname . ' ' . $lastname;
                            ?>
                        </small>
                    </li>
                </div>
            </ul>
        </div>
    </div>

    <!-- Desktop Sidebar -->
    <div id="sidebarDesktop">
        <ul class="nav nav-tabs flex-column">

            <a href="user.php">
                <img class="logo" src="../assets/images/dnscfindr-logo.png" alt="logo">
            </a>

            <div class="top-nav">
                <li class="nav-item">
                    <a href="#" class="nav-link back-btn" onclick="history.back(); return false;">
                        <i class="fa-solid fa-angle-left"></i>
                        Back
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#changePassword">
                        <i class="fa-solid fa-key"></i>
                        Change Password
                    </a>
                </li>
            </div>

            <div class="bottom-nav">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#create-post">
                        <i class="fa-solid fa-pen-clip"></i>
                        Create Post
                    </a>
                </li>

                <li class="user-profile dropup">
                    <a href="" class="profile" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-circle-user"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="profile.php">
                                <i class="fa-solid fa-circle-user"></i>
                                Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="fa-solid fa-gear"></i>
                                Settings
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="../app/logout.php">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                logout
                            </a>
                        </li>
                    </ul>
                    <small class="name">
                            <?php
                                echo $firstname . ' ' . $lastname;
                            ?>
                        </small>
                </li>
            </div>
        </ul>
    </div>
    
    <section id="section">
        <header>
            <div class="left-actions">
                <div class="menuToggleButtonContainer">
                    <i class="fa-solid fa-bars menuToggleButton" data-bs-toggle="offcanvas"
                        data-bs-target="#sidebarMobile"></i>
                </div>
                <h1 class="page-title">Settings</h1>
            </div>

            <div class="right-actions">
                <a href="">
                    <i class="fa-regular fa-message"></i>
                </a>
                <a href="">
                    <i class="fa-regular fa-bell"></i>
                </a>
                <a href="">
                    <i class="fa-regular fa-calendar"></i>
                </a>
            </div>
        </header>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="changePassword">
                <form method="POST" action="../app/change_update_password.php">
                    <h2>
                        <i class="fa-solid fa-key"></i>
                        Change Password
                    </h2>

                    <div class="input-box">
                        <input type="password" name="old_password" placeholder="Current Password" required>
                        <i class="fa-solid fa-eye-slash toggle-password"></i>
                    </div>

                    <div class="input-box">
                        <input type="password" name="new_password" placeholder="New Password" required>
                        <i class="fa-solid fa-eye-slash toggle-password"></i>
                    </div>

                    <div class="input-box">
                        <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
                        <i class="fa-solid fa-eye-slash toggle-password"></i>
                    </div>

                    <button type="submit" class="btn primary-btn">
                        Update Password
                    </button>
                </form>
            </div>
        </div>
    </section>
</body>

<!-- Js -->
<script src="../assets/js/script.js"></script>

<script>
document.querySelectorAll(".toggle-password").forEach(icon => {
    icon.addEventListener("click", function () {
        const input = this.parentElement.querySelector("input");

        if (input.type === "password") {
            input.type = "text";
            this.classList.remove("fa-eye-slash");
            this.classList.add("fa-eye");
        } else {
            input.type = "password";
            this.classList.remove("fa-eye");
            this.classList.add("fa-eye-slash");
        }
    });
});

setTimeout(() => {
    const alertBox = document.getElementById("passwordAlert");

    if (alertBox) {
        alertBox.classList.remove("show");
        alertBox.classList.add("hide");

        setTimeout(() => {
            alertBox.remove();
        }, 500); 
    }
}, 5000);
</script>