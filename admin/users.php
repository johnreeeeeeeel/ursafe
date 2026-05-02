<?php
session_start();
require '../app/db_connection.php';

if (!isset($_SESSION['id']) || !isset($_SESSION['email'])) {
    header('Location: ../index.php');
    exit;
}

// Set session variables
$id = $_SESSION['id'] ?? '';

$lastname = $_SESSION['lastname'] ?? '';
$firstname = $_SESSION['firstname'] ?? '';
$middlename = $_SESSION['middlename'] ?? '';

$sex = $_SESSION['sex'] ?? '';
$dob = $_SESSION['dob'] ?? '';

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

    <title>UrSafe - Users</title>

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
    <link rel="stylesheet" href="../assets/css/admin.css">
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
        <div class="alert alert-<?= $_SESSION['alert_message']['type'] ?> alert-dismissible fade show" id="messageAlert">
            <?= $_SESSION['alert_message']['text'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <?php unset($_SESSION['alert_message']); ?>
    <?php endif; ?>
    
    <!-- Mobile Sidebar -->
    <nav class="offcanvas offcanvas-start" id="sidebarMobile">
        <div class="offcanvas-body">
            <ul class="nav">
                <a href="dashboard.php">
                    <img class="logo" src="../assets/images/ursafe_logo_2.png" alt="logo">
                </a>

                <div class="top-nav">
                    <li>
                        <a class="link" href="dashboard.php">
                            <i class="fa-solid fa-chart-simple"></i>
                            Dashboard
                        </a>
                    </li>

                    <li>
                        <a class="link" href="lockers.php">
                            <i class="fa-solid fa-vault"></i>
                            Lockers
                        </a>
                    </li>

                    <li>
                        <a class="link active" href="users.php">
                            <i class="fa-solid fa-users"></i>
                            Users
                        </a>
                    </li>
                </div>

                <div class="bottom-nav">
                    <li>
                        <a class="link danger-btn" data-bs-toggle="modal" data-bs-target="#logoutConfirmationModal">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            logout
                        </a>
                    </li>
                </div>
            </ul>
        </div>
    </nav>

    <!-- Desktop Sidebar -->
    <nav id="sidebarDesktop">
        <ul class="nav">
            <a href="dashboard.php">
                <img class="logo" src="../assets/images/ursafe_logo_2.png" alt="logo">
            </a>

            <div class="top-nav">
                <li>
                    <a class="link" href="dashboard.php">
                        <i class="fa-solid fa-chart-simple"></i>
                        Dashboard
                    </a>
                </li>

                <li>
                    <a class="link" href="lockers.php">
                        <i class="fa-solid fa-vault"></i>
                        Lockers
                    </a>
                </li>

                <li>
                    <a class="link active" href="users.php">
                        <i class="fa-solid fa-users"></i>
                        Users
                    </a>
                </li>
            </div>

            <div class="bottom-nav">
               <li>
                    <a class="link danger-btn" data-bs-toggle="modal" data-bs-target="#logoutConfirmationModal">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        logout
                    </a>
                </li>
            </div>
        </ul>
    </nav>

    <section id="section">
        <header>
            <div class="menuToggleButtonContainer">
                <i class="fa-solid fa-bars menuToggleButton" data-bs-toggle="offcanvas" data-bs-target="#sidebarMobile"></i>
            </div>

            <h1 class="page-title">Users</h1>
        </header>

        <div class="content">
            <div id="users">

                <!-- Users table -->
                <div class="table-container">
                    <div class="table-header">
                        <div class="search-group">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <form action="" method="GET">
                                <input type="search" name="searchUser" id="searchUser" placeholder="Search users..." value="<?= htmlspecialchars($_GET['searchUser'] ?? '') ?>">
                            </form>
                        </div>

                        <div class="filter-group">
                            <div class="filter-group">
                                <i class="fa-solid fa-filter"></i>

                                <form method="GET" action="users.php">
                                    <input type="hidden" name="searchUser">

                                    <select name="status">

                                    </select>
                                </form>
                            </div>
                        </div>
                    </div>

                    <table class="table table-borderless">
                        <thead>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Fullname</th>
                            <th>Email</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            <?php
                                $search_term = $_GET['searchUser'] ?? '';

                                if (!empty($search_term)) {
                                    $stmt = $conn_local->prepare("SELECT * FROM view_users WHERE fullname LIKE ? OR email LIKE ?");
                                    $like = "%" . $search_term . "%";
                                    $stmt->bind_param("ss", $like, $like);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                } else {
                                    require __DIR__ . '/../app/search_filter_users.php';
                                }
                            ?>

                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td data-label="ID"><?= $row['id']; ?></td>
                                    <td data-label="Username"><?= $row['username']; ?></td>
                                    <td data-label="Fullname"><?= $row['fullname']; ?></td>
                                    <td data-label="Email"><?= $row['email']; ?></td>
                                    <td data-label="Action">
                                        <div class="action-buttons">
                                            <button type="button" class="sm-btn primary-btn" data-bs-toggle="modal" data-bs-target="#viewUserModal" 
                                                onclick="viewUserDetails(
                                                    '<?= $row['id'] ?>', 
                                                    '<?= $row['fullname'] ?>', 
                                                    '<?= $row['sex'] ?>', 
                                                    '<?= $row['dob'] ?>', 
                                                    '<?= $row['institute'] ?>', 
                                                    '<?= $row['program'] ?>', 
                                                    '<?= $row['username'] ?>', 
                                                    '<?= $row['email'] ?>',
                                                )">
                                                <i class="fa-solid fa-eye"></i>
                                                View
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- View user details modal -->
        <div class="modal fade" id="viewUserModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="profile-container">
                            <div class="profile">
                                <div class="profile-icon">
                                    <i class="fa-solid fa-circle-user"></i>
                                    <span class="badge rounded-pill" id="vu_userrole"></span>
                                </div>
                                <h5><span id="vu_fullname"></span></h5>
                            </div>
                            
                            
                            <div class="profile-section">
                                <h6>Personal Information</h6>
                                <p>
                                    <small><b>User ID: </b><span id="vu_id"></span></small>
                                    <small><b>Username: </b><span id="vu_username"></span></small>
                                </p>
                                <p>
                                    <small><b>Sex: </b><span id="vu_sex"></span></small>
                                    <small><b>Data of Birth: </b><span id="vu_dob"></span></small>
                                </p>
                            </div>

                            <div class="profile-section">
                                <h6>Academic Information</h6>
                                <p>
                                    <small><b>Institute: </b><span id="vu_institute"></span></small>
                                    <small><b>Program: </b><span id="vu_program"></span></small>
                                </p>
                            </div>

                            <div class="profile-section">
                                <h6>Account Information</h6>
                                <p>
                                    <small><b>Email: </b><span id="vu_email"></span></small>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logout confirmation modal -->
        <div class="modal fade danger-modal" id="logoutConfirmationModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            Logout Confirmation
                        </h5>
                    </div>

                    <div class="modal-body">
                        <p>Are you sure you want to logout?</p>
                        
                        <div class="action-buttons">
                            <a href="../app/logout.php" class="btn primary-btn">
                                Yes, Logout
                            </a>
                            
                            <button type="button" class="btn secondary-btn" data-bs-dismiss="modal">
                                No
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>

<!-- Js -->
<script src="../assets/js/script.js"></script>
