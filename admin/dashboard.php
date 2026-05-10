<?php
session_start();
require '../app/db_connection.php';

if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {

} else {
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>UrSafe - Dashboard</title>

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
                <a class="logo-container" href="dashboard.php">
                    <img class="logo" src="../assets/images/ursafe_logo_2.png" alt="logo">
                </a>

                <div class="top-nav">
                    <li>
                        <a class="link active" href="dashboard.php">
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
                        <a class="link" href="users.php">
                            <i class="fa-solid fa-users"></i>
                            Users
                        </a>
                    </li>
                </div>

                <div class="bottom-nav">
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
            <a class="logo-container" href="dashboard.php">
                <img class="logo" src="../assets/images/ursafe_logo_2.png" alt="logo">
            </a>

            <div class="top-nav">
                <li>
                    <a class="link active" href="dashboard.php">
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
                    <a class="link" href="users.php">
                        <i class="fa-solid fa-users"></i>
                        Users
                    </a>
                </li>
            </div>

            <div class="bottom-nav">
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
                <h1 class="page-title">Dashboard</h1> 
            </div>
        </header>

        <div class="content">
            <div id="dashboard">
                <?php
                    // Get users count
                    $stmtUsersCount = $conn_local->prepare("SELECT COUNT(*) AS users_count FROM users");
                    $stmtUsersCount->execute();

                    $usersCountResultSet = $stmtUsersCount->get_result();
                    $usersCountRow = $usersCountResultSet->fetch_assoc();

                    $stmtUsersCount->close();
                    $conn_local->next_result();

                    // Get students count
                    $stmtStudentsCount = $conn_remote->prepare("SELECT COUNT(*) AS students_count FROM students");
                    $stmtStudentsCount->execute();

                    $studentsCountResultSet = $stmtStudentsCount->get_result();
                    $studentsCountRow = $studentsCountResultSet->fetch_assoc();

                    $stmtStudentsCount->close();
                    $conn_local->next_result();

                    // Get total lockers
                    $stmtTotalLockersCount = $conn_local->prepare("SELECT COUNT(*) AS total_lockers_count FROM locker_slots");
                    $stmtTotalLockersCount->execute();

                    $totalLockersCountResultSet = $stmtTotalLockersCount->get_result();
                    $totalLockersCountRow = $totalLockersCountResultSet->fetch_assoc();

                    $stmtTotalLockersCount->close();
                    $conn_local->next_result();

                    // Get available lockers
                    $stmtAvailableLockersCount = $conn_local->prepare("SELECT COUNT(*) AS available_lockers_count FROM locker_slots WHERE status = 'Available'");
                    $stmtAvailableLockersCount->execute();

                    $availableLockersCountResultSet = $stmtAvailableLockersCount->get_result();
                    $availableLockersCountRow = $availableLockersCountResultSet->fetch_assoc();

                    $stmtAvailableLockersCount->close();
                    $conn_local->next_result();

                    // Get occupied lockers
                    $stmtOccupiedLockersCount = $conn_local->prepare("SELECT COUNT(*) AS occupied_lockers_count FROM locker_slots WHERE status = 'Occupied'");
                    $stmtOccupiedLockersCount->execute();

                    $occupiedLockersCountResultSet = $stmtOccupiedLockersCount->get_result();
                    $occupiedLockersCountRow = $occupiedLockersCountResultSet->fetch_assoc();

                    $stmtOccupiedLockersCount->close();
                    $conn_local->next_result();

                    // Get total locker application
                    $stmtTotalLockerApplicationsCount = $conn_local->prepare("SELECT COUNT(*) AS total_locker_applications_count FROM locker_applications");
                    $stmtTotalLockerApplicationsCount->execute();

                    $totalLockerApplicationsCountResultSet = $stmtTotalLockerApplicationsCount->get_result();
                    $totalLockerApplicationsCountRow = $totalLockerApplicationsCountResultSet->fetch_assoc();

                    $stmtTotalLockerApplicationsCount->close();
                    $conn_local->next_result();

                    // Get pending locker application
                    $stmtPendingLockerApplicationsCount = $conn_local->prepare("SELECT COUNT(*) AS pending_locker_applications_count FROM locker_applications WHERE status = 'Pending'");
                    $stmtPendingLockerApplicationsCount->execute();

                    $pendingLockerApplicationsCountResultSet = $stmtPendingLockerApplicationsCount->get_result();
                    $pendingLockerApplicationsCountRow = $pendingLockerApplicationsCountResultSet->fetch_assoc();

                    $stmtPendingLockerApplicationsCount->close();
                    $conn_local->next_result();

                    // Get cancelled locker application
                    $stmtCancelledLockerApplicationsCount = $conn_local->prepare("SELECT COUNT(*) AS cancelled_locker_applications_count FROM locker_applications WHERE status = 'Cancelled'");
                    $stmtCancelledLockerApplicationsCount->execute();

                    $cancelledLockerApplicationsCountResultSet = $stmtCancelledLockerApplicationsCount->get_result();
                    $cancelledLockerApplicationsCountRow = $cancelledLockerApplicationsCountResultSet->fetch_assoc();

                    $stmtCancelledLockerApplicationsCount->close();
                    $conn_local->next_result();

                    // Get accepted locker application
                    $stmtAcceptedLockerApplicationsCount = $conn_local->prepare("SELECT COUNT(*) AS accepted_locker_applications_count FROM locker_applications WHERE status = 'Accepted'");
                    $stmtAcceptedLockerApplicationsCount->execute();

                    $acceptedLockerApplicationsCountResultSet = $stmtAcceptedLockerApplicationsCount->get_result();
                    $acceptedLockerApplicationsCountRow = $acceptedLockerApplicationsCountResultSet->fetch_assoc();

                    $stmtAcceptedLockerApplicationsCount->close();
                    $conn_local->next_result();

                    // Get rejected locker application
                    $stmtRejectedLockerApplicationsCount = $conn_local->prepare("SELECT COUNT(*) AS rejected_locker_applications_count FROM locker_applications WHERE status = 'Rejected'");
                    $stmtRejectedLockerApplicationsCount->execute();

                    $rejectedLockerApplicationsCountResultSet = $stmtRejectedLockerApplicationsCount->get_result();
                    $rejectedLockerApplicationsCountRow = $rejectedLockerApplicationsCountResultSet->fetch_assoc();

                    $stmtRejectedLockerApplicationsCount->close();
                    $conn_local->next_result();

                    // Get revoked locker application
                    $stmtRevokedLockerApplicationsCount = $conn_local->prepare("SELECT COUNT(*) AS revoked_locker_applications_count FROM locker_applications WHERE status = 'Revoked'");
                    $stmtRevokedLockerApplicationsCount->execute();

                    $revokedLockerApplicationsCountResultSet = $stmtRevokedLockerApplicationsCount->get_result();
                    $revokedLockerApplicationsCountRow = $revokedLockerApplicationsCountResultSet->fetch_assoc();

                    $stmtRevokedLockerApplicationsCount->close();
                    $conn_local->next_result();
                ?>

                <div class="data-count-container">
                    <!-- Activated users -->
                    <div class="data-count">
                        <div class="count">
                            <small>Activated Users</small>

                            <h1>
                                <span><?php echo $usersCountRow['users_count']; ?></span>
                                <span><i class="fa-solid fa-users"></i></span>
                            </h1>
                        </div>

                        <small>
                            Over <?php echo $studentsCountRow['students_count']; ?> students enrolled
                        </small>
                    </div>
                    
                    <!-- Lockers -->
                    <div class="data-count">
                        <div class="count">
                            <small>Total Lockers</small>

                            <h1>
                                <span><?php echo $totalLockersCountRow['total_lockers_count']; ?></span>
                                <span><i class="fa-solid fa-vault"></i></span>
                            </h1>
                        </div>

                        <small>
                            Over <?php echo $availableLockersCountRow['available_lockers_count']; ?> available lockers and <?php echo $occupiedLockersCountRow['occupied_lockers_count']; ?> occupied lockers
                        </small>
                    </div>

                    <!-- Locker applications -->
                    <div class="data-count">
                        <div class="count">
                            <small>Accepted Application</small>

                            <h1>
                                <span><?php echo $acceptedLockerApplicationsCountRow['accepted_locker_applications_count']; ?></span>
                                <span><i class="fa-solid fa-circle-check"></i></span>
                            </h1>
                        </div>

                        <small>
                            Over <?php echo $pendingLockerApplicationsCountRow['pending_locker_applications_count']; ?> pending locker applications
                        </small>
                    </div>
                </div>
            </div>  
        </div>
    </section>

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
