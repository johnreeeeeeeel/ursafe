<?php
session_start();

$fieldErrors = $_SESSION['field_error'] ?? [];
unset($_SESSION['field_error']);
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

    <div class="auth-container">
        <!-- Login form -->
        <div class="auth-form" id="loginForm">
            <img src="assets/images/ursafe_logo_2.png" alt="reload">

            <form method="POST" action="app/auth/login.php">
                <div class="form-group">
                    <div class="input-box">
                        <input type="email" id="loginEmail" class="input" placeholder="Email" name="loginEmail" value="<?= $_SESSION['login_email'] ?? '' ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="input-box">
                        <input type="password" id="loginPassword" class="input" placeholder="Password" name="loginPassword" required>
                        <i class="fa-solid fa-eye-slash toggle-password"></i>
                    </div>
                    <span class="field-error">
                        <?= $fieldErrors['email_or_password'] ?? '' ?>
                    </span>
                </div>

                <a class="forgot-password" onclick="window.location.hash='reset_password'; showAuthForms();">Forgot Password?</a>

                <button type="submit" class="btn primary-btn">
                    Login
                </button>
            </form>

            <button type="button" class="btn secondary-btn" onclick="window.location.hash='activate_account'; showAuthForms();">
                Activate Account
            </button>
        </div>

        <!-- Account activation form -->
        <div class="auth-form" style="display: none;" id="accountActivationForm">
            <form method="POST" action="app/auth/activate_account.php">
                <h2>Activate account</h2>

                <div class="form-group">
                    <label class="input-label">Email</label>
                    <div class="input-box">
                        <input type="email" name="accountActivationEmail" placeholder="doe.john@example.com" value="<?= $_SESSION['account_activation_email'] ?? '' ?>" required>
                    </div>
                    <span class="field-error">
                        <?= $fieldErrors['email'] ?? '' ?>
                    </span>
                </div>

                <div class="form-group">
                    <label class="input-label">Username</label>
                    <div class="input-box">
                        <input type="text" name="accountActivationUsername" placeholder="john_doe" value="<?= $_SESSION['account_activation_username'] ?? '' ?>" required>
                    </div>
                    <span class="field-error">
                        <?= $fieldErrors['username'] ?? '' ?>
                    </span>
                </div>

                <div class="form-group">
                    <label class="input-label">Password</label>
                    <div class="input-box">
                        <input type="password" id="password" name="accountActivationPassword" placeholder="12345678" value="<?= $_SESSION['account_activation_password'] ?? '' ?>" required>
                        <i class="fa-solid fa-eye-slash toggle-password"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label class="input-label">Confirm Password</label>
                    <div class="input-box">
                        <input type="password" name="accountActivationConfirmPassword" placeholder="12345678" value="<?= $_SESSION['account_activation_confirm_password'] ?? '' ?>" required>
                        <i class="fa-solid fa-eye-slash toggle-password"></i>
                    </div>
                    <span class="field-error">
                        <?= $fieldErrors['confirm_password'] ?? '' ?>
                    </span>
                </div>

                <button type="submit" class="btn primary-btn">
                    Activate Account
                </button>
            </form>

            <button type="button" class="btn secondary-btn" onclick="window.location.hash='login'; showAuthForms();">
                Already have an account
            </button>
        </div>

        <!-- Reset password form -->
        <div class="auth-form" style="display: none;" id="resetPasswordForm">
            <form method="POST" action="app/otp/send_otp.php">
                <h2>Reset password</h2>

                <div class="form-group">
                    <label class="input-label">Email</label>
                    <div class="input-box">
                        <input type="email" name="resetPasswordEmail" placeholder="doe.john@example.com" value="<?= $_SESSION['reset_password_email'] ?? '' ?>" required>
                    </div>
                    <span class="field-error">
                        <?= $fieldErrors['email'] ?? '' ?>
                    </span>
                </div>

                <button type="submit" class="btn primary-btn">
                    Send OTP
                </button>
            </form>

            <button type="button" class="btn secondary-btn" onclick="window.location.hash='login'; showAuthForms();">
                Cancel
            </button>
        </div>
    </div>

    <!-- Success account activation -->
    <?php if (isset($_SESSION['show_success_account_activation_modal'])): ?>
        <script>
            window.addEventListener('DOMContentLoaded', function () {
                var modal = new bootstrap.Modal(document.getElementById('successAccountActivation'));
                modal.show();
            });
        </script>
        <?php unset($_SESSION['show_success_account_activation_modal']); ?>
    <?php endif; ?>

    <div class="modal fade success-message" id="successAccountActivation">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="info">
                        <i class="fa-solid fa-square-check"></i>
                        <h4>Account activated</h4>
                        <p>Welcome to UrSafe! Your account has been activated successfully. You may now sign in and get started.</p>
                    </div>

                    <div class="action-buttons">
                        <button type="button" class="btn primary-btn" data-bs-dismiss="modal">
                            Okay, Get Started
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success password reset -->
    <?php if (isset($_SESSION['show_success_password_reset_modal'])): ?>
        <script>
            window.addEventListener('DOMContentLoaded', function () {
                var modal = new bootstrap.Modal(document.getElementById('successPasswordReset'));
                modal.show();
            });
        </script>
        <?php unset($_SESSION['show_success_password_reset_modal']); ?>
    <?php endif; ?>

    <div class="modal fade success-message" id="successPasswordReset">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="info">
                        <i class="fa-solid fa-square-check"></i>
                        <h4>Check your email</h4>
                        <p>We have sent a temporary password to your email address. Please check your inbox and use it to sign in to your UrSafe account.</p>
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
</body>

<!-- Js -->
<script src="assets/js/script.js"></script>

<script>
function showAuthForms() {
    var activationForm = document.getElementById("accountActivationForm");
    var loginForm = document.getElementById("loginForm");
    var resetPasswordForm = document.getElementById("resetPasswordForm");

    if (window.location.hash === "#activate_account") {
        loginForm.querySelector('input[name="loginEmail"]').value = "";
        loginForm.querySelector('input[name="loginPassword"]').value = "";

        activationForm.style.display = "flex";
        loginForm.style.display = "none";
        resetPasswordForm.style.display = "none";

    } else if (window.location.hash === "#reset_password") {
        loginForm.querySelector('input[name="loginEmail"]').value = "";
        loginForm.querySelector('input[name="loginPassword"]').value = "";

        activationForm.style.display = "none";
        loginForm.style.display = "none";
        resetPasswordForm.style.display = "flex";

    } else {
        activationForm.querySelector('input[name="accountActivationEmail"]').value = "";
        activationForm.querySelector('input[name="accountActivationUsername"]').value = "";
        activationForm.querySelector('input[name="accountActivationPassword"]').value = "";
        activationForm.querySelector('input[name="accountActivationConfirmPassword"]').value = "";

        resetPasswordForm.querySelector('input[name="resetPasswordEmail"]').value = "";

        activationForm.style.display = "none";
        resetPasswordForm.style.display = "none";
        loginForm.style.display = "flex";
    }
}

showAuthForms();
</script>

</html>
