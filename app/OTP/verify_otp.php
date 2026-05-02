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
    <div id="loadingScreen" class="loading-screen d-none">
        <div class="spinner-grow"></div>
    </div>

    <div class="verify-otp-container">
        <form method="POST" action="check_otp.php">
            <h2>
                <a onclick="history.back()">
                    <i class="fa-solid fa-angle-left"></i>
                </a>
                Verify OTP
            </h2>

            <small>Please enter the OTP sent to your email.</small>
            
            <div class="form-group">
                <div class="input-box">
                    <input type="text" name="otp" placeholder="Enter OTP" required class="form-control">
                </div>
            </div>

            <button type="submit" class="btn primary-btn">
                Verify OTP
            </button>
        </form> 
    </div>
</body>

<!-- Js -->
<script src="../../assets/js/script.js"></script>