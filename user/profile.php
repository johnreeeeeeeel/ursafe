<?php
session_start();
require '../app/db_connection.php';

$fieldErrors = $_SESSION['field_error'] ?? [];
unset($_SESSION['field_error']);

if (isset($_SESSION['role']) && $_SESSION['role'] == 'user') {

} else {
    header("Location: ../index.php");
    exit;
}

// Set session variables
$id = $_SESSION['id'];

$lastname = $_SESSION['lastname'] ?? '';
$firstname = $_SESSION['firstname'] ?? '';
$middlename = $_SESSION['middlename'] ?? '';

$fullname = $firstname . ' ' . (!empty($middlename) ? $middlename . ' ' : '') . $lastname;

$sex = $_SESSION['sex'] ?? '';
$dob = isset($_SESSION['dob']) ? date("M d, Y", strtotime($_SESSION['dob'])) : '';

$institute = $_SESSION['institute'] ?? '';
$program = $_SESSION['program'] ?? '';

$username = $_SESSION['username'] ?? '';
$email = $_SESSION['email'] ?? '';
$password = $_SESSION['password'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>UrSafe - Profile</title>

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
    <!-- Loading Screen -->
    <div id="loadingScreen" class="loading-screen d-none">
        <div class="spinner-grow"></div>
    </div>
    
    <!-- Alert messege -->
    <?php if (isset($_SESSION['alert_message'])): ?>
        <div class="toast align-items-center <?= $_SESSION['alert_message']['type'] ?> show" role="alert" aria-live="assertive" aria-atomic="true" id="messageAlert">
            <div class="d-flex">
                <div class="toast-body">
                    <?php if ($_SESSION['alert_message']['type'] == 'danger') : ?>
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <?= $_SESSION['alert_message']['text'] ?>
                    <?php elseif ($_SESSION['alert_message']['type'] == 'warning') : ?>
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <?= $_SESSION['alert_message']['text'] ?>
                     <?php else : ?>
                        <i class="fa-solid fa-circle-check"></i>
                        <?= $_SESSION['alert_message']['text'] ?>
                    <?php endif; ?>
                </div>

                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
        <?php unset($_SESSION['alert_message']); ?>
    <?php endif; ?>
    
    <!-- Mobile Sidebar -->
    <nav class="offcanvas offcanvas-start" id="sidebarMobile">
        <div class="offcanvas-body">
            <ul class="nav">
                <a class="logo-container" href="home.php">
                    <img class="logo" src="../assets/images/ursafe_logo_2.png" alt="logo">
                </a>

                <div class="top-nav">
                    <li>
                        <a class="link" href="home.php">
                            <i class="fa-solid fa-chart-simple"></i>
                            Home
                        </a>
                    </li>

                    <li>
                        <a class="link" href="lockers.php">
                            <i class="fa-solid fa-vault"></i>
                            Lockers
                        </a>
                    </li>
                    
                    <li>
                        <a class="link" href="my_applications.php">
                            <i class="fa-solid fa-file-lines"></i>
                            My Applications
                        </a>
                    </li> 
                </div>

                <div class="bottom-nav">
                    <li>
                        <a class="link active" href="profile.php">
                            <i class="fa-solid fa-user"></i>
                            Profile
                        </a>
                    </li>

                    <li>
                        <a class="link danger-btn" data-bs-toggle="modal" data-bs-target="#logoutConfirmationModal">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            Logout
                        </a>
                    </li>
                </div>
            </ul>
        </div>
    </nav>

    <!-- Desktop Sidebar -->
    <nav id="sidebarDesktop">
        <ul class="nav">
            <a class="logo-container" href="home.php">
                <img class="logo" src="../assets/images/ursafe_logo_2.png" alt="logo">
            </a>

            <div class="top-nav">
                <li>
                    <a class="link" href="home.php">
                        <i class="fa-solid fa-chart-simple"></i>
                        Home
                    </a>
                </li>

                <li>
                    <a class="link" href="lockers.php">
                        <i class="fa-solid fa-vault"></i>
                        Lockers
                    </a>
                </li>

                <li>
                    <a class="link" href="my_applications.php">
                        <i class="fa-solid fa-file-lines"></i>
                        My Applications
                    </a>
                </li> 
            </div>

            <div class="bottom-nav">
                <li>
                    <a class="link active" href="profile.php">
                        <i class="fa-solid fa-user"></i>
                        Profile
                    </a>
                </li>

                <li>
                    <a class="link danger-btn" data-bs-toggle="modal" data-bs-target="#logoutConfirmationModal">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        Logout
                    </a>
                </li>
            </div>
        </ul>
    </nav>
    
    <section id="section">
        <header>
            <div class="left">
                <i class="fa-solid fa-bars menuToggleButton" data-bs-toggle="offcanvas" data-bs-target="#sidebarMobile"></i>
                <h1 class="page-title">Profile</h1> 
            </div>
        </header>

        <div class="content">
            <div id="profile">
                <div class="profile-container" style="display: none;">
                    <div class="profile">
                        <i class="fa-solid fa-circle-user"></i>
                        <h4><span><?= htmlspecialchars($fullname)?></span></h4>
                        <small>@<?= htmlspecialchars($username)?> | <?= htmlspecialchars($id)?></small>
                    </div>

                    <hr>
                    
                    <div class="profile-section">
                        <h6>Personal Information</h6>
                        <p>
                            <small><b>Sex: </b><span><?= htmlspecialchars($sex)?></span></small>
                            <small><b>Date of Birth: </b><span><?= htmlspecialchars($dob)?></span></small>
                        </p>
                    </div>

                    <hr>

                    <div class="profile-section">
                        <h6>Academic Information</h6>
                        <p>
                            <small><b>Institute: </b><span><?= htmlspecialchars($institute)?></span></small>
                        </p>
                        <p>
                            <small><b>Program: </b><span><?= htmlspecialchars($program)?></span></small>
                        </p>
                    </div>

                    <hr>

                    <div class="profile-section">
                        <h6>Account Information</h6>
                        <p>
                            <small><b>Email: </b><span><?= htmlspecialchars($email)?></span></small>
                        </p>
                    </div>

                    <hr>

                    <button class="btn secondary-btn" onclick="window.location.hash='change_password'; showProfileSections();">
                        <i class="fa-solid fa-key"></i>
                        Change Password
                    </button>
                </div>

                <div class="change-password-form" id="changePasswordForm">
                    <form method="POST" action="../app/auth/change_update_password.php">
                        <h2>Change Password</h2>

                        <div class="form-group">
                            <label class="input-label">Current Password</label>
                            <div class="input-box">
                                <input type="password" name="current_password" placeholder="eg., 123456" required>
                                <i class="fa-solid fa-eye-slash toggle-password"></i>
                            </div>
                            <span class="field-error">
                                <?= $fieldErrors['current_password'] ?? '' ?>
                            </span>
                        </div>

                        <div class="form-group">
                            <label class="input-label">New Password</label>
                            <div class="input-box">
                                <input type="password" name="new_password" placeholder="eg., 000000" required>
                                <i class="fa-solid fa-eye-slash toggle-password"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">Confirm New Password</label>
                            <div class="input-box">
                                <input type="password" name="confirm_password" placeholder="eg., 000000" required>
                                <i class="fa-solid fa-eye-slash toggle-password"></i>
                            </div>
                            <span class="field-error">
                                <?= $fieldErrors['confirm_password'] ?? '' ?>
                            </span>
                        </div>

                        <div class="action-buttons">
                            <button type="submit" class="btn primary-btn">
                                Update Password
                            </button>

                            <button type="button" class="btn secondary-btn" onclick="window.location.hash='profile'; showProfileSections();">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>  
        </div>
    </section>

    <!-- Success password change -->
    <?php if (isset($_SESSION['show_success_password_change_modal'])): ?>
        <script>
            window.addEventListener('DOMContentLoaded', function () {
                var modal = new bootstrap.Modal(document.getElementById('successPasswordChange'));
                modal.show();
            });
        </script>
        <?php unset($_SESSION['show_success_password_change_modal']); ?>
    <?php endif; ?>

    <div class="modal fade success-message" id="successPasswordChange">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="info">
                        <i class="fa-solid fa-square-check"></i>
                        <h4>Password has been changed</h4>
                        <p>Your password has been changed successfully. You can now continue using your UrSafe account with your new password.</p>
                    </div>

                    <div class="action-buttons">
                        <button type="button" class="btn primary-btn" data-bs-dismiss="modal">
                            Okay, Got It
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout confirmation modal -->
    <div class="modal fade danger-modal" id="logoutConfirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="message">
                        <p><i class="fa-solid fa-circle-exclamation"></i></p>
                        <h5>Logout</h5>
                        <p>Are you sure you want to logout?</p>
                    </div>
                    
                    <div class="action-buttons">
                        <button type="button" class="btn secondary-btn" data-bs-dismiss="modal">
                            No
                        </button>

                        <a href="../app/auth/logout.php" class="btn primary-btn">
                            Yes, Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

<!-- Js -->
<script src="../assets/js/script.js"></script>

<script>
function showProfileSections() {
    var profileContainer = document.querySelector(".profile-container");
    var changePasswordForm = document.getElementById("changePasswordForm");

    if (window.location.hash === "#change_password") {
        profileContainer.style.display = "none";
        changePasswordForm.style.display = "flex";
    } else {
        changePasswordForm.querySelector('input[name="current_password"]').value = "";
        changePasswordForm.querySelector('input[name="new_password"]').value = "";
        changePasswordForm.querySelector('input[name="confirm_password"]').value = "";

        profileContainer.style.display = "flex";
        changePasswordForm.style.display = "none";
    }
}

showProfileSections();
</script>