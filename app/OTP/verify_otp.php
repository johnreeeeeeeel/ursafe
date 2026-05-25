<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>UrSafe</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Changa+One:ital@0;1&family=Outfit:wght@100..900&display=swap"
        rel="stylesheet">

    <!-- Favicon -->
    <link rel="shortcut icon" href="../../assets/images/favicon.ico" type="image/x-icon">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="../../assets/css/index.css">
    <link rel="stylesheet" href="../../assets/css/components.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

    <style>
        
    </style>
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

    <div class="verify-otp-container">
        <form method="POST" action="check_otp.php">
            <h2>
                Verify
            </h2>
            <h2>
                One Time Pin
            </h2>

            <small>We have sent a one time pin to your email.</small>
            
            <div class="inputs">
                <input type="text" name="otp[]" class="input1" maxlength="1" inputmode="numeric" pattern="[0-9]*">
                <input type="text" name="otp[]" class="input2" maxlength="1" inputmode="numeric" pattern="[0-9]*">
                <input type="text" name="otp[]" class="input3" maxlength="1" inputmode="numeric" pattern="[0-9]*">
                <input type="text" name="otp[]" class="input4" maxlength="1" inputmode="numeric" pattern="[0-9]*">
                <input type="text" name="otp[]" class="input5" maxlength="1" inputmode="numeric" pattern="[0-9]*">
                <input type="text" name="otp[]" class="input6" maxlength="1" inputmode="numeric" pattern="[0-9]*">
            </div>

            <div class="action-buttons">
                <a href="../../index.php" class="btn secondary-btn">
                    <i class="fa-solid fa-angle-left"></i>
                    Back to Login
                </a>

                <button type="submit" class="btn primary-btn">
                    Verify OTP
                </button>
            </div>
        </form> 
    </div>
</body>

<!-- Js -->
<script src="../../assets/js/script.js"></script>