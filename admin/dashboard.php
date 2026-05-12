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
                    $stmtUsersCount = $conn_local->prepare("CALL get_users_count()");
                    $stmtUsersCount->execute();

                    $usersCountResultSet = $stmtUsersCount->get_result();
                    $usersCountRow = $usersCountResultSet->fetch_assoc();

                    $stmtUsersCount->close();
                    $conn_local->next_result();

                    // Get students count
                    $stmtStudentsCount = $conn_remote->prepare("CALL get_students_count()");
                    $stmtStudentsCount->execute();

                    $studentsCountResultSet = $stmtStudentsCount->get_result();
                    $studentsCountRow = $studentsCountResultSet->fetch_assoc();

                    $stmtStudentsCount->close();
                    $conn_local->next_result();

                    // Get total lockers
                    $stmtTotalLockersCount = $conn_local->prepare("CALL get_total_lockers_count()");
                    $stmtTotalLockersCount->execute();

                    $totalLockersCountResultSet = $stmtTotalLockersCount->get_result();
                    $totalLockersCountRow = $totalLockersCountResultSet->fetch_assoc();

                    $stmtTotalLockersCount->close();
                    $conn_local->next_result();

                    // Get available lockers
                    $stmtAvailableLockersCount = $conn_local->prepare("CALL get_available_lockers_count()");
                    $stmtAvailableLockersCount->execute();

                    $availableLockersCountResultSet = $stmtAvailableLockersCount->get_result();
                    $availableLockersCountRow = $availableLockersCountResultSet->fetch_assoc();

                    $stmtAvailableLockersCount->close();
                    $conn_local->next_result();

                    // Get occupied lockers
                    $stmtOccupiedLockersCount = $conn_local->prepare("CALL get_occupied_lockers_count()");
                    $stmtOccupiedLockersCount->execute();

                    $occupiedLockersCountResultSet = $stmtOccupiedLockersCount->get_result();
                    $occupiedLockersCountRow = $occupiedLockersCountResultSet->fetch_assoc();

                    $stmtOccupiedLockersCount->close();
                    $conn_local->next_result();

                    // Get total locker application
                    $stmtTotalLockerApplicationsCount = $conn_local->prepare("CALL get_total_locker_applications_count()");
                    $stmtTotalLockerApplicationsCount->execute();

                    $totalLockerApplicationsCountResultSet = $stmtTotalLockerApplicationsCount->get_result();
                    $totalLockerApplicationsCountRow = $totalLockerApplicationsCountResultSet->fetch_assoc();

                    $stmtTotalLockerApplicationsCount->close();
                    $conn_local->next_result();

                    // Get pending locker application
                    $stmtPendingLockerApplicationsCount = $conn_local->prepare("CALL get_pending_locker_applications_count()");
                    $stmtPendingLockerApplicationsCount->execute();

                    $pendingLockerApplicationsCountResultSet = $stmtPendingLockerApplicationsCount->get_result();
                    $pendingLockerApplicationsCountRow = $pendingLockerApplicationsCountResultSet->fetch_assoc();

                    $stmtPendingLockerApplicationsCount->close();
                    $conn_local->next_result();

                    // Get cancelled locker application
                    $stmtCancelledLockerApplicationsCount = $conn_local->prepare("CALL get_cancelled_locker_applications_count()");
                    $stmtCancelledLockerApplicationsCount->execute();

                    $cancelledLockerApplicationsCountResultSet = $stmtCancelledLockerApplicationsCount->get_result();
                    $cancelledLockerApplicationsCountRow = $cancelledLockerApplicationsCountResultSet->fetch_assoc();

                    $stmtCancelledLockerApplicationsCount->close();
                    $conn_local->next_result();

                    // Get accepted locker application
                    $stmtAcceptedLockerApplicationsCount = $conn_local->prepare("CALL get_accepted_locker_applications_count()");
                    $stmtAcceptedLockerApplicationsCount->execute();

                    $acceptedLockerApplicationsCountResultSet = $stmtAcceptedLockerApplicationsCount->get_result();
                    $acceptedLockerApplicationsCountRow = $acceptedLockerApplicationsCountResultSet->fetch_assoc();

                    $stmtAcceptedLockerApplicationsCount->close();
                    $conn_local->next_result();

                    // Get rejected locker application
                    $stmtRejectedLockerApplicationsCount = $conn_local->prepare("CALL get_rejected_locker_applications_count()");
                    $stmtRejectedLockerApplicationsCount->execute();

                    $rejectedLockerApplicationsCountResultSet = $stmtRejectedLockerApplicationsCount->get_result();
                    $rejectedLockerApplicationsCountRow = $rejectedLockerApplicationsCountResultSet->fetch_assoc();

                    $stmtRejectedLockerApplicationsCount->close();
                    $conn_local->next_result();

                    // Get revoked locker application
                    $stmtRevokedLockerApplicationsCount = $conn_local->prepare("CALL get_revoked_locker_applications_count()");
                    $stmtRevokedLockerApplicationsCount->execute();

                    $revokedLockerApplicationsCountResultSet = $stmtRevokedLockerApplicationsCount->get_result();
                    $revokedLockerApplicationsCountRow = $revokedLockerApplicationsCountResultSet->fetch_assoc();

                    $stmtRevokedLockerApplicationsCount->close();
                    $conn_local->next_result();

                    // Get recent activated user
                    $stmtRecentActivatedUser = $conn_local->prepare("CALL get_recent_user_account_activation()");
                    $stmtRecentActivatedUser->execute();

                    $recentActivatedUserResultSet = $stmtRecentActivatedUser->get_result();
                    $recentActivatedUserRow = $recentActivatedUserResultSet->fetch_assoc();

                    $stmtRecentActivatedUser->close();
                    $conn_local->next_result();

                    // Get recent locker application
                    $stmtRecentLockerApplication = $conn_local->prepare("CALL get_recent_locker_application()");
                    $stmtRecentLockerApplication->execute();

                    $recentLockerApplicationResultSet = $stmtRecentLockerApplication->get_result();

                    $stmtRecentLockerApplication->close();
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
                            <small>Accepted Applications</small>

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

                <div class="recent-container">
                    <!-- Recent user activation -->
                    <div class="recent-user-account-activation">
                        <h4>Recent User Activation</h4>

                        <?php if ($recentActivatedUserRow): ?>
                            <div class="profile">
                                <i class="fa-solid fa-circle-user"></i>
                                
                                <h6>
                                    <?php
                                        echo htmlspecialchars(
                                            $recentActivatedUserRow['firstname'] . ' ' .
                                            $recentActivatedUserRow['middlename'] . ' ' .
                                            $recentActivatedUserRow['lastname']
                                        );
                                    ?>
                                </h6>
                                <small>
                                    <span>ID: </span>
                                    <?php echo htmlspecialchars($recentActivatedUserRow['id']); ?>
                                </small>
                            </div>

                            <div class="profile-details">
                                <small>
                                    <span>Date: </span>
                                    <?php echo htmlspecialchars($recentActivatedUserRow['created_at']); ?>
                                </small>
                            </div>
                        <?php else: ?>

                            <small>No recent activated user found.</small>
                        <?php endif; ?>
                    </div>

                    <!-- Recent locker application -->
                    <div class="recent-locker-application"> 
                        <h4>Recent Locker Applications</h4>

                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Application ID</th>
                                    <th>User ID</th>
                                    <th>Location</th>
                                    <th>Slot</th>
                                    <th>Size</th>
                                    <th>Price</th>
                                    <th>Date</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if ($recentLockerApplicationResultSet->num_rows > 0): ?>
                                    <?php while ($recentLockerApplicationRow = $recentLockerApplicationResultSet->fetch_assoc()): ?>
                                        <tr>
                                            <td data-label="Application ID"><?= $recentLockerApplicationRow['id'] ?></td>
                                            <td data-label="User ID"><?= $recentLockerApplicationRow['user_id'] ?></td>
                                            <td data-label="Location"><?= $recentLockerApplicationRow['location'] ?></td>
                                            <td data-label="Slot"><?= $recentLockerApplicationRow['slot_number'] ?></td>
                                            <td data-label="Size"><?= $recentLockerApplicationRow['size'] ?></td>
                                            <td data-label="Price"><?= $recentLockerApplicationRow['price'] ?></td>
                                            <td data-label="Date"><?= $recentLockerApplicationRow['created_at'] ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>

                                <small>No recent locker application found.</small>
                            <?php endif; ?>
                            </tbody>
                        </table>
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
