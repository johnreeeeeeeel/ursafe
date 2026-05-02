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
    <!-- Loading Screen -->
    <div id="loadingScreen" class="loading-screen d-none">
        <div class="spinner-grow"></div>
    </div>

    <!-- Mobile Sidebar -->
    <div class="offcanvas offcanvas-start" id="sidebarMobile">

        <div class="offcanvas-body">
            <ul class="nav nav-tabs flex-column">

                <a href="user.php">
                    <img class="logo" src="../assets/images/dnscfindr-logo.png" alt="logo">
                </a>

                <div class="top-nav">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#home" data-title="Home"
                            onclick="setTitle(this)">
                            <i class="fa-solid fa-home"></i>
                            Home
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#myPosts" data-title="My Posts"
                            onclick="setTitle(this)">
                            <i class="fa-solid fa-file"></i>
                            My Posts
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
                                <a class="dropdown-item" href="settings.php">
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
                    <a class="nav-link active" data-bs-toggle="tab" href="#home" data-title="Home"
                        onclick="setTitle(this)">
                        <i class="fa-solid fa-home"></i>
                        Home
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#myPosts" data-title="My Posts"
                        onclick="setTitle(this)">
                        <i class="fa-solid fa-file"></i>
                        My Posts
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
                            <a class="dropdown-item" href="settings.php">
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
                <h1 class="page-title">Home</h1>
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
            <div class="tab-pane fade show active" id="home">
                <h1>Home</h1>
            </div>

            <div class="tab-pane fade" id="myPosts">
                <h1>My Posts</h1>
            </div>
        </div>
    </section>
</body>

<!-- Js -->

<script src="../assets/js/script.js"></script>