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
    <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="stylesheet" href="assets/css/components.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
</head>

<body class="container-fluid">
    <!-- Loading Spinner -->
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

    <div class="auth-container">
        <img src="assets/images/ursafe_logo_2.png" alt="reload">

        <!-- Login Form -->
        <div class="auth-form">
            <form method="POST" action="app/auth/login.php">
                <div class="input-box">
                    <input type="email" id="loginEmail" class="input" placeholder="Email" name="loginEmail" required>
                </div>

                <div class="input-box">
                    <input type="password" id="loginPassword" class="input" placeholder="Password" name="loginPassword" required>
                    <i class="fa-solid fa-eye-slash toggle-password"></i>
                </div>

                <a class="forgot-password" data-bs-toggle="modal" data-bs-target="#lostPasswordModal">Forgot Password?</a>

                <button type="submit" class="btn primary-btn">
                    Login
                </button>
            </form>

            <button type="submit" class="btn secondary-btn" data-bs-toggle="modal" data-bs-target="#activateAccountModal">
                Activate Account
            </button>
        </div>
    </div>
    
    <!-- Activate account modal -->
    <div class="modal fade success-modal" id="activateAccountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa-solid fa-user-plus"></i>
                        Activate Account
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form method="POST" action="app/auth/activate_account.php">
                
                        <div class="form-group">
                            <label class="input-label">Email</label>
                            <div class="input-box">
                                <input type="email" name="email" placeholder="doe.john@example.com" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">Username</label>
                            <div class="input-box">
                                <input type="text" name="username" placeholder="john_doe" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">Password</label>
                            <div class="input-box">
                                <input type="password" id="password" name="password" placeholder="12345678" required>
                                <i class="fa-solid fa-eye-slash toggle-password"></i>
                            </div>
                        </div>

                        <button type="submit" class="btn primary-btn">
                            Activate Account
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Reset password modal -->
    <div class="modal fade primary-modal" id="lostPasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa-solid fa-key"></i>
                        Forgot Your Password?
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <!--Send OTP -->
                    <form method="POST" action="app/otp/send_otp.php">
                
                        <div class="form-group">
                            <label class="input-label">Email</label>
                            <div class="input-box">
                                <input type="email" name="email" placeholder="Enter your email" required>
                            </div>
                        </div>

                        <button type="submit" class="btn primary-btn">
                            Send OTP
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

<!-- Js -->
<script src="assets/js/script.js"></script>

</html>
