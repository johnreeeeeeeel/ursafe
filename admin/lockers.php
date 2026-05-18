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

    <title>UrSafe - Lockers</title>

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
                        <a class="link" href="dashboard.php">
                            <i class="fa-solid fa-chart-simple"></i>
                            Dashboard
                        </a>
                    </li>

                    <li>
                        <a class="link active" href="lockers.php">
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
                    <a class="link" href="dashboard.php">
                        <i class="fa-solid fa-chart-simple"></i>
                        Dashboard
                    </a>
                </li>

                <li>
                    <a class="link active" href="lockers.php">
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
                <h1 class="page-title">Lockers</h1> 
            </div>

            <div class="right">
                <div class="dropdown">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                        View
                    </button>
                    
                    <ul class="dropdown-menu"> 
                        <li>
                            <button type="button" class="dropdown-item" data-bs-toggle="offcanvas" data-bs-target="#lockerPendingApplicationOffcanvas" onclick="window.location.hash='lockerPendingApplicationOffcanvas';">
                                Pending Application
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item" data-bs-toggle="offcanvas" data-bs-target="#lockerAcceptedApplicationOffcanvas" onclick="window.location.hash='lockerAcceptedApplicationOffcanvas';">
                                Accepted Application
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item" data-bs-toggle="offcanvas" data-bs-target="#lockerApplicationHistoryOffcanvas" onclick="window.location.hash='lockerApplicationHistoryOffcanvas';">
                                Application History
                            </button>
                        </li>
                        <li><hr class="dropdown-divider"></hr></li>
                        <li>
                            <button type="button" class="dropdown-item" data-bs-toggle="offcanvas" data-bs-target="#lockerLocationsOffcanvas" onclick="window.location.hash='lockerLocationsOffcanvas';">
                                Locker Locations
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item" data-bs-toggle="offcanvas" data-bs-target="#lockerSizesOffcanvas" onclick="window.location.hash='lockerSizesOffcanvas';">
                                Locker Sizes
                            </button> 
                        </li>
                        <li><hr class="dropdown-divider"></hr></li>
                        <li>
                            <button type="button" class="dropdown-item" data-bs-toggle="offcanvas" data-bs-target="#lockerLogsOffcanvas" onclick="window.location.hash='lockerLogsOffcanvas';">
                                Locker Logs
                            </button> 
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <div class="content">
            <div id="lockers">
                <!-- Pending application offcanvas -->
                <div class="offcanvas offcanvas-end" id="lockerPendingApplicationOffcanvas">
                    <?php
                        // Pending locker application
                        $search = trim($_GET['searchPendingApplication'] ?? '');
                        $filter = strtolower($_GET['filterPendingApplication'] ?? '');

                        if (!empty($search) || !empty($filter)) {
                            $stmtPending = $conn_local->prepare("CALL getSearchFilterPendingLockerApplications(?, ?)");
                            $stmtPending->bind_param("ss", $search, $filter);
                        } else {
                            $stmtPending = $conn_local->prepare("CALL getPendingLockerApplications()");
                        }

                        $stmtPending->execute();
                        $pendingResultSet = $stmtPending->get_result();
                        $stmtPending->close();

                        $conn_local->next_result();
                        $conn_local->store_result();

                        while ($conn_local->next_result()) { $conn_local->store_result(); }
                    ?>

                    <div class="offcanvas-header">
                        <h3 class="offcanvas-title">Pending Applications</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                    </div>

                    <div class="offcanvas-body">
                        <header>
                            <form method="GET">
                                <div class="search-group">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    <input type="search"
                                        name="searchPendingApplication"
                                        placeholder="Search..."
                                        value="<?= htmlspecialchars($_GET['searchPendingApplication'] ?? '') ?>">
                                </div>

                                <div class="filter-group">
                                    <i class="fa-solid fa-filter"></i>
                                    <select name="filterPendingApplication" onchange="this.form.submit()">
                                        <option value="">All</option>
                                        <option value="newest" <?= (($_GET['filterPendingApplication'] ?? '') === 'newest') ? 'selected' : '' ?>>Newest</option>
                                        <option value="oldest" <?= (($_GET['filterPendingApplication'] ?? '') === 'oldest') ? 'selected' : '' ?>>Oldest</option>
                                    </select>
                                </div>

                                <button type="submit" hidden></button>
                            </form>
                        </header>

                        <div class="card-container">
                            <?php if ($pendingResultSet->num_rows > 0): ?>
                                <div class="cards">
                                    <?php while ($row = $pendingResultSet->fetch_assoc()) { ?>
                                        <div class="card pending">
                                            <div class="card-header">
                                                <h4>Slot <?= $row['slot_number'] ?></h4>
                                                <p><?= $row['location'] ?></p>

                                                <span class="badge rounded-pill pending-badge"><?= $row['status'] ?></span>
                                            </div>

                                            <div class="card-body">
                                                <div class="locker-details">
                                                    <p class="label">
                                                        <i class="fa-solid fa-vault"></i>
                                                        Locker Details
                                                    </p>
                                                    <p>Size: <?= $row['size'] ?></p>
                                                    <p>Price: &#8369;<?= $row['price'] ?></p>
                                                    <p>Start on: <?= $row['start_at'] ?></p>
                                                    <p>End on: <?= $row['end_at'] ?></p>
                                                </div>
                                                
                                                <div class="user-details">
                                                    <p class="label">
                                                        <i class="fa-solid fa-address-card"></i>
                                                        User Details
                                                    </p>

                                                    <p>User ID: #<?= $row['user_id'] ?></p>
                                                    <p>Full Name: <?= $row['fullname'] ?></p>
                                                </div>

                                                <div class="application-details">
                                                    <p class="label">
                                                        <i class="fa-brands fa-jxl"></i>
                                                        Application Details
                                                    </p>

                                                    <p>Application ID: #<?= $row['application_id'] ?></p>
                                                    <p>Applied on: <?= $row['created_at'] ?></p>
                                                </div>
                                            </div>

                                            <div class="card-footer">
                                                <div class="action-buttons">
                                                    <button class="sm-btn primary-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#acceptLockerApplicationModal<?= $row['application_id'] ?>">
                                                        <i class="fa-solid fa-check"></i>
                                                        Accept
                                                    </button>

                                                    <button class="sm-btn danger-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#rejectLockerApplicationModal<?= $row['application_id'] ?>">
                                                        <i class="fa-solid fa-xmark"></i>
                                                        Reject
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php else: ?>
                                <div id="empty">
                                    <i class="fa-solid fa-ban"></i>
                                    <small>No pending application yet</small>
                                    <small>Try to reload page</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php foreach ($pendingResultSet as $row) { ?>
                    <!-- Accept locker application modals -->
                    <div class="modal fade primary-modal" id="acceptLockerApplicationModal<?= $row['application_id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div class="message">
                                        <i class="fa-solid fa-circle-check"></i>
                                        <h5>Accept Application</h5>
                                        <p>Are you sure you want to accept <span>application <?= $row['application_id'] ?></span>?</p>
                                    </div>

                                    <div class="action-buttons">
                                        <button type="button" class="btn secondary-btn" data-bs-dismiss="modal">
                                            Cancel
                                        </button>

                                        <form method="POST" action="../app/locker/accept_locker_application.php">
                                            <input type="hidden" name="id" value="<?= $row['application_id'] ?>">

                                            <button type="submit" class="btn primary-btn">
                                                Yes, Accept
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reject locker application modals -->
                    <div class="modal fade danger-modal" id="rejectLockerApplicationModal<?= $row['application_id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div class="message">
                                        <i class="fa-solid fa-circle-xmark"></i>
                                        <h5>Reject Application</h5>
                                        <p>Are you sure you want to reject <span>application <?= $row['application_id'] ?></span>?</p>
                                    </div>

                                    <div class="action-buttons">
                                        <button type="button" class="btn secondary-btn" data-bs-dismiss="modal">
                                            Cancel
                                        </button>

                                        <form method="POST" action="../app/locker/reject_locker_application.php">
                                            <input type="hidden" name="id" value="<?= $row['application_id'] ?>">

                                            <button type="submit" class="btn primary-btn">
                                                Yes, Reject
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <!-- Accepted application offcanvas -->
                <div class="offcanvas offcanvas-end" id="lockerAcceptedApplicationOffcanvas">
                    <?php
                        // Accepted locker application
                        $search = trim($_GET['searchAcceptedApplication'] ?? '');
                        $filter = strtolower($_GET['filterAcceptedApplication'] ?? '');

                        if (!empty($search) || !empty($filter)) {
                            $stmtAccepted = $conn_local->prepare("CALL getSearchFilterAcceptedLockerApplications(?, ?)");
                            $stmtAccepted->bind_param("ss", $search, $filter);
                        } else {
                            $stmtAccepted = $conn_local->prepare("CALL getAcceptedLockerApplications()");
                        }

                        $stmtAccepted->execute();
                        $acceptedResultSet = $stmtAccepted->get_result();
                        $stmtAccepted->close();

                        $conn_local->next_result();
                        $conn_local->store_result();

                        while ($conn_local->next_result()) { $conn_local->store_result(); }
                    ?>

                    <div class="offcanvas-header">
                        <h3 class="offcanvas-title">Accepted Applications</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                    </div>

                    <div class="offcanvas-body">
                        <header>
                            <form method="GET">
                                <div class="search-group">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    <input type="search"
                                        name="searchAcceptedApplication"
                                        placeholder="Search..."
                                        value="<?= htmlspecialchars($_GET['searchAcceptedApplication'] ?? '') ?>">
                                </div>

                                <div class="filter-group">
                                    <i class="fa-solid fa-filter"></i>
                                    <select name="filterAcceptedApplication" onchange="this.form.submit()">
                                        <option value="">All</option>
                                        <option value="newest" <?= (($_GET['filterAcceptedApplication'] ?? '') === 'newest') ? 'selected' : '' ?>>Newest</option>
                                        <option value="oldest" <?= (($_GET['filterAcceptedApplication'] ?? '') === 'oldest') ? 'selected' : '' ?>>Oldest</option>
                                    </select>
                                </div>

                                <button type="submit" hidden></button>
                            </form>
                        </header>

                        <div class="card-container">
                            <?php if ($acceptedResultSet->num_rows > 0): ?>
                                <div class="cards">
                                    <?php while ($row = $acceptedResultSet->fetch_assoc()) { ?>
                                        <div class="card accepted">
                                            <div class="card-header">
                                                <h4>Slot <?= $row['slot_number'] ?></h4>
                                                <p><?= $row['location'] ?></p>

                                                <span class="badge rounded-pill accepted-badge"><?= $row['status'] ?></span>
                                            </div>

                                            <div class="card-body">
                                                <div class="locker-details">
                                                    <p class="label">
                                                        <i class="fa-solid fa-vault"></i>
                                                        Locker Details
                                                    </p>

                                                    <p>Size: <?= $row['size'] ?></p>
                                                    <p>Price: &#8369;<?= $row['price'] ?></p>
                                                    <p>Start on: <?= $row['start_at'] ?></p>
                                                    <p>End on: <?= $row['end_at'] ?></p>
                                                </div>

                                                <div class="user-details">
                                                    <p class="label">
                                                        <i class="fa-solid fa-address-card"></i>
                                                        User Details
                                                    </p>

                                                    <p>User ID: #<?= $row['user_id'] ?></p>
                                                    <p>Full Name: <?= $row['fullname'] ?></p>
                                                </div>

                                                <div class="application-details">
                                                    <p class="label">
                                                        <i class="fa-brands fa-jxl"></i>
                                                        Application Details
                                                    </p>

                                                    <p>Application ID: #<?= $row['application_id'] ?></p>
                                                    <p>Accepted on: <?= $row['updated_at'] ?></p>
                                                </div>
                                            </div>

                                            <div class="card-footer">
                                                <div class="action-buttons">
                                                    <button class="sm-btn danger-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#revokeLockerApplicationModal<?= $row['application_id'] ?>">
                                                        <i class="fa-solid fa-ban"></i>
                                                        Revoke
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php else: ?>
                                <div id="empty">
                                    <i class="fa-solid fa-ban"></i>
                                    <small>No accepted application yet</small>
                                    <small>Try to reload page</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php foreach ($acceptedResultSet as $row) { ?>
                    <!-- Revoke locker application modals -->
                    <div class="modal fade danger-modal" id="revokeLockerApplicationModal<?= $row['application_id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div class="message">
                                        <i class="fa-solid fa-ban"></i>
                                        <h5>Revoke Application</h5>
                                        <p>Are you sure you want to revoke <span>application <?= $row['application_id'] ?></span>?</p>
                                    </div>

                                    <div class="action-buttons">
                                        <button type="button" class="btn secondary-btn" data-bs-dismiss="modal">
                                            Cancel
                                        </button>

                                        <form method="POST" action="../app/locker/revoke_locker_application.php">
                                            <input type="hidden" name="id" value="<?= $row['application_id'] ?>">

                                            <button type="submit" class="btn primary-btn">
                                                Yes, Revoke
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <!-- Application history offcanvas -->
                <div class="offcanvas offcanvas-end" id="lockerApplicationHistoryOffcanvas">
                    <?php
                        // Locker application history 
                        $search = trim($_GET['searchApplicationHistory'] ?? '');
                        $filter = strtolower($_GET['filterApplicationHistory'] ?? '');

                        if (!empty($search) || !empty($filter)) {
                            $stmtHistory = $conn_local->prepare("CALL getSearchFilterLockerApplicationHistory(?, ?)");
                            $stmtHistory->bind_param("ss", $search, $filter);
                        } else {
                            $stmtHistory = $conn_local->prepare("CALL getLockerApplicationHistory()");
                        }

                        $stmtHistory->execute();
                        $historyResultSet = $stmtHistory->get_result();
                        $stmtHistory->close();

                        $conn_local->next_result();
                        $conn_local->store_result();

                        while ($conn_local->next_result()) { $conn_local->store_result(); }
                    ?>
                    
                    <div class="offcanvas-header">
                        <h3 class="offcanvas-title">Application History</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                    </div>

                    <div class="offcanvas-body">
                        <header>
                            <form method="GET">
                                <div class="search-group">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    <input type="search"
                                        name="searchApplicationHistory"
                                        placeholder="Search..."
                                        value="<?= htmlspecialchars($_GET['searchApplicationHistory'] ?? '') ?>">
                                </div>

                                <div class="filter-group">
                                    <i class="fa-solid fa-filter"></i>
                                    <select name="filterApplicationHistory" onchange="this.form.submit()">
                                        <option value="">All</option>
                                        <option value="newest" <?= (($_GET['filterApplicationHistory'] ?? '') === 'newest') ? 'selected' : '' ?>>Newest</option>
                                        <option value="oldest" <?= (($_GET['filterApplicationHistory'] ?? '') === 'oldest') ? 'selected' : '' ?>>Oldest</option>
                                    </select>
                                </div>

                                <button type="submit" hidden></button>
                            </form>
                        </header>

                        <div class="locker-applications-container">
                            <?php if ($historyResultSet->num_rows > 0): ?>
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th>Application ID</th>
                                            <th>User ID</th>
                                            <th>Fullname</th>
                                            <th>Location</th>
                                            <th>Slot</th>
                                            <th>Size</th>
                                            <th>Price</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php while ($row = $historyResultSet->fetch_assoc()) { ?>
                                            <tr>
                                                <td data-label="Application ID"><?= $row['application_id'] ?></td>
                                                <td data-label="User ID"><?= $row['user_id'] ?></td>
                                                <td data-label="Fullname"><?= $row['fullname'] ?></td>
                                                <td data-label="Location"><?= $row['location'] ?></td>
                                                <td data-label="Slot"><?= $row['slot_number'] ?></td>
                                                <td data-label="Size"><?= $row['size'] ?></td>
                                                <td data-label="Price">&#8369;<?= $row['price'] ?></td>
                                                <td data-label="Start Date"><?= $row['start_at'] ?></td>
                                                <td data-label="End Date"><?= $row['end_at'] ?></td>

                                                <td data-label="Status">
                                                    <?php if ($row['status'] == 'Revoked') { ?>
                                                        <span class="badge rounded-pill revoked-badge">Revoked</span>
                                                    <?php } elseif ($row['status'] == 'Cancelled') { ?>
                                                        <span class="badge rounded-pill cancelled-badge">Cancelled</span>
                                                    <?php } elseif ($row['status'] == 'Ended') { ?>
                                                        <span class="badge rounded-pill ended-badge">Ended</span>
                                                    <?php } else { ?>
                                                        <span class="badge rounded-pill rejected-badge">Rejected</span>
                                                    <?php } ?>
                                                </td>
                                                
                                                <td data-label="Date"><?= $row['updated_at'] ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div id="empty">
                                    <i class="fa-solid fa-ban"></i>
                                    <small>No application history yet</small>
                                    <small>Try to reload page</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Offcanvas for locker locations -->
                <div class="offcanvas offcanvas-end" id="lockerLocationsOffcanvas">
                    <?php
                        // Get locker locations
                        $search = trim($_GET['searchLockerLocations'] ?? '');
                        $filter = strtolower($_GET['filterLockerLocations'] ?? '');

                        if (!empty($search) || !empty($filter)) {
                            // Use search and filter
                            $stmtLockerLocation = $conn_local->prepare("CALL getSearchFilterLockerLocation(?, ?)");
                            $stmtLockerLocation->bind_param("ss", $search, $filter);
                        } else {
                            // Use raw
                            $stmtLockerLocation = $conn_local->prepare("CALL getLockerLocations()");
                        }

                        $stmtLockerLocation->execute();
                        $lockerLocationResultSet = $stmtLockerLocation->get_result();
                        $stmtLockerLocation->close();

                        $conn_local->next_result();
                        $conn_local->store_result();
                    ?>

                    <div class="offcanvas-header">
                        <div>
                            <h3 class="offcanvas-title">Lockers Locations</h3>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                    </div>

                    <div class="offcanvas-body">
                        <header>
                            <form method="GET">
                                <div class="search-group">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    <input type="search"
                                        name="searchLockerLocations"
                                        placeholder="Search locker locations..."
                                        value="<?= htmlspecialchars($_GET['searchLockerLocations'] ?? '') ?>">
                                </div>

                                <div class="filter-group">
                                    <i class="fa-solid fa-filter"></i>
                                    <select name="filterLockerLocations" onchange="this.form.submit()">
                                        <option value="">All</option>
                                        <option value="a-z" <?= (($_GET['filterLockerLocations'] ?? '') === 'a-z') ? 'selected' : '' ?>>A - Z</option>
                                        <option value="z-a" <?= (($_GET['filterLockerLocations'] ?? '') === 'z-a') ? 'selected' : '' ?>>Z - A</option>
                                        <option value="newest" <?= (($_GET['filterLockerLocations'] ?? '') === 'newest') ? 'selected' : '' ?>>Newest</option>
                                        <option value="oldest" <?= (($_GET['filterLockerLocations'] ?? '') === 'oldest') ? 'selected' : '' ?>>Oldest</option>
                                    </select>
                                </div>

                                <button type="submit" hidden></button>
                            </form>

                            <button type="button" class="btn primary-btn" data-bs-toggle="modal" data-bs-target="#addLockerLocationModal">
                                <i class="fa-solid fa-plus"></i>
                                Add Locker Location
                            </button>
                        </header>

                        <div class="table-container">
                            <?php if ($lockerLocationResultSet->num_rows > 0): ?>
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th>Location</th>
                                            <th>Date Created</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php while ($lockerLocationRow = $lockerLocationResultSet->fetch_assoc()) { ?>
                                            <tr>
                                                <td data-label="Location"><?= $lockerLocationRow['location'] ?></td>
                                                <td data-label="Date Created"><?= $lockerLocationRow['created_at'] ?></td>

                                                <td data-label="Action">
                                                    <div class="action-buttons">
                                                        <button class="sm-btn primary-btn"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#updateLockerLocationModal<?= $lockerLocationRow['id'] ?>">
                                                            <i class="fa-solid fa-pen-to-square"></i>
                                                            Edit
                                                        </button>

                                                        <button class="sm-btn danger-btn"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteLockerLocationModal<?= $lockerLocationRow['id'] ?>">
                                                            <i class="fa-solid fa-trash"></i>
                                                            Delete
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div id="empty">
                                    <i class="fa-solid fa-ban"></i>
                                    <small>No locker location yet</small>
                                    <small>Try to reload page</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Add locker location modal -->
                <div class="modal fade primary-modal" id="addLockerLocationModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="fa-solid fa-plus"></i>
                                    Add Locker Location
                                </h5>
                            </div>

                            <div class="modal-body">
                                <form method="POST" action="../app/locker/add_locker_location.php">
                                    <div class="form-group">
                                        <label class="input-label">Location Name</label>
                                        <div class="input-box">
                                            <input type="text" name="location" required>
                                        </div>
                                    </div>

                                    <div class="action-buttons">
                                        <button type="submit" class="btn primary-btn">Add Location</button>
                                        <button type="button" class="btn secondary-btn" data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                    $lockerLocationResultSet = $conn_local->query("CALL getLockerLocations()");
                    $conn_local->next_result();

                    while ($lockerLocationRow = $lockerLocationResultSet->fetch_assoc()) {
                ?>

                    <!-- Update locker location modal -->
                    <div class="modal fade primary-modal" id="updateLockerLocationModal<?= $lockerLocationRow['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        Update Location
                                    </h5>
                                </div>

                                <div class="modal-body">
                                    <form method="POST" action="../app/locker/update_locker_location.php">
                                        <input type="hidden" name="id" value="<?= $lockerLocationRow['id'] ?>">

                                        <div class="form-group">
                                            <label class="input-label">Location</label>
                                            <div class="input-box">
                                                <input type="text" name="location" value="<?= $lockerLocationRow['location'] ?>" required>
                                            </div>
                                        </div>

                                        <div class="action-buttons">
                                            <button type="submit" class="btn primary-btn">Save</button>
                                            <button type="button" class="btn secondary-btn" data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Delete locker location modal -->
                    <div class="modal fade danger-modal" id="deleteLockerLocationModal<?= $lockerLocationRow['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div class="message">
                                        <p><i class="fa-solid fa-circle-exclamation"></i></p>
                                        <h5>Delete</h5>
                                        <p>Are you sure you want to delete <span><?= $lockerLocationRow['location'] ?></span>?</p>
                                    </div>

                                    <div class="action-buttons">
                                        <button class="btn secondary-btn" data-bs-dismiss="modal">Cancel</button>

                                        <form method="POST" action="../app/locker/delete_locker_location.php">
                                            <input type="hidden" name="id" value="<?= $lockerLocationRow['id'] ?>">
                                            <button type="submit" class="btn danger-btn">Yes, Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <!-- Offcanvas for locker sizes -->
                <div class="offcanvas offcanvas-end" id="lockerSizesOffcanvas">
                    <?php
                        // Get locker sizes
                        $search = trim($_GET['searchLockerSizes'] ?? '');
                        $filter = strtolower($_GET['filterLockerSizes'] ?? '');

                        if (!empty($search) || !empty($filter)) {
                            // Use search and filter
                            $stmtLockerSizes = $conn_local->prepare("CALL getSearchFilterLockerSizes(?, ?)");
                            $stmtLockerSizes->bind_param("ss", $search, $filter);
                        } else {
                            // Use raw
                            $stmtLockerSizes = $conn_local->prepare("CALL getLockerSizes()");
                        }

                        $stmtLockerSizes->execute();
                        $lockerSizesResultSet = $stmtLockerSizes->get_result();
                        $stmtLockerSizes->close();

                        $conn_local->next_result();
                        $conn_local->store_result();
                    ?>

                    <div class="offcanvas-header">
                        <h3 class="offcanvas-title">Lockers Sizes & Prices</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                    </div>

                    <div class="offcanvas-body">
                        <header>
                            <form method="GET">
                                <div class="search-group">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    <input type="search"
                                        name="searchLockerSizes"
                                        placeholder="Search locker sizes..."
                                        value="<?= htmlspecialchars($_GET['searchLockerSizes'] ?? '') ?>">
                                </div>

                                <div class="filter-group">
                                    <i class="fa-solid fa-filter"></i>
                                    <select name="filterLockerSizes" onchange="this.form.submit()">
                                        <option value="">All</option>
                                        <option value="a-z" <?= (($_GET['filterLockerSizes'] ?? '') === 'a-z') ? 'selected' : '' ?>>A - Z</option>
                                        <option value="z-a" <?= (($_GET['filterLockerSizes'] ?? '') === 'z-a') ? 'selected' : '' ?>>Z - A</option>
                                        <option value="newest" <?= (($_GET['filterLockerSizes'] ?? '') === 'newest') ? 'selected' : '' ?>>Newest</option>
                                        <option value="oldest" <?= (($_GET['filterLockerSizes'] ?? '') === 'oldest') ? 'selected' : '' ?>>Oldest</option>
                                        <option value="cheaper" <?= (($_GET['filterLockerSizes'] ?? '') === 'cheaper') ? 'selected' : '' ?>>Cheaper</option>
                                        <option value="expensive" <?= (($_GET['filterLockerSizes'] ?? '') === 'expensive') ? 'selected' : '' ?>>Expensive</option>
                                    </select>
                                </div>

                                <button type="submit" hidden></button>
                            </form>
                            <button type="button" class="btn primary-btn" data-bs-toggle="modal" data-bs-target="#addLockerSizeModal">
                                <i class="fa-solid fa-plus"></i>
                                Add Locker Size 
                            </button>
                        </header>

                        <div class="table-container">
                            <?php if ($lockerSizesResultSet->num_rows > 0): ?>
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th>Size</th>
                                            <th>Price</th>
                                            <th>Date Created</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php while ($lockerSizesRow = $lockerSizesResultSet->fetch_assoc()) { ?>
                                            <tr>
                                                <td data-label="Size"><?= $lockerSizesRow['size'] ?></td>
                                                <td data-label="Price">&#8369;<?= $lockerSizesRow['price'] ?></td>
                                                <td data-label="Date Created"><?= $lockerSizesRow['created_at'] ?></td>

                                                <td data-label="Action">
                                                    <div class="action-buttons">
                                                        <button class="sm-btn primary-btn"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#updateLockerSizeModal<?= $lockerSizesRow['id'] ?>">
                                                            <i class="fa-solid fa-pen-to-square"></i>
                                                            Edit
                                                        </button>

                                                        <button class="sm-btn danger-btn"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteLockerSizeModal<?= $lockerSizesRow['id'] ?>">
                                                            <i class="fa-solid fa-trash"></i>
                                                            Delete
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div id="empty">
                                    <i class="fa-solid fa-ban"></i>
                                    <small>No locker size yet</small>
                                    <small>Try to reload page</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Add locker size modal -->
                <div class="modal fade primary-modal" id="addLockerSizeModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="fa-solid fa-plus"></i>
                                    Add Locker Size
                                </h5>
                            </div>

                            <div class="modal-body">
                                <form method="POST" action="../app/locker/add_locker_size.php">
                                    <div class="form-group">
                                        <label class="input-label">Size Name</label>
                                        <div class="input-box">
                                            <input type="text" name="size" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="input-label">Price</label>
                                        <div class="input-box">
                                            <input type="number" name="price" required>
                                        </div>
                                    </div>

                                    <div class="action-buttons">
                                        <button type="submit" class="btn primary-btn">Add Size</button>
                                        <button type="button" class="btn secondary-btn" data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                    $lockerSizesResultSet = $conn_local->query("CALL getLockerSizes()");
                    $conn_local->next_result();

                    while ($lockerSizesRow = $lockerSizesResultSet->fetch_assoc()) {
                ?>
                    <!-- Update locker size modal -->
                    <div class="modal fade primary-modal" id="updateLockerSizeModal<?= $lockerSizesRow['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        Update Size
                                    </h5>
                                </div>

                                <div class="modal-body">
                                    <form method="POST" action="../app/locker/update_locker_size.php">
                                        <input type="hidden" name="id" value="<?= $lockerSizesRow['id'] ?>">

                                        <div class="form-group">
                                            <label class="input-label">Size</label>
                                            <div class="input-box">
                                                <input type="text" name="size" value="<?= $lockerSizesRow['size'] ?>" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="input-label">Type</label>
                                            <div class="input-box">
                                                <input type="number" step="0.01" name="price" value="<?= $lockerSizesRow['price'] ?>" required>
                                            </div>
                                        </div>

                                        <div class="action-buttons">
                                            <button type="submit" class="btn primary-btn">Save</button>
                                            <button type="button" class="btn secondary-btn" data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Delete locker size modal -->
                    <div class="modal fade danger-modal" id="deleteLockerSizeModal<?= $lockerSizesRow['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div class="message">
                                        <p><i class="fa-solid fa-circle-exclamation"></i></p>
                                        <h5>Delete</h5>
                                        <p>Are you sure you want to delete <span><?= $lockerSizesRow['size'] ?></span>?</p>
                                    </div>

                                    <div class="action-buttons">
                                        <button class="btn secondary-btn" data-bs-dismiss="modal">Cancel</button>

                                        <form method="POST" action="../app/locker/delete_locker_size.php">
                                            <input type="hidden" name="id" value="<?= $lockerSizesRow['id'] ?>">

                                            <button type="submit" class="btn danger-btn">Yes, Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <!-- Offcanvas for locker logs -->
                <div class="offcanvas offcanvas-end" id="lockerLogsOffcanvas">
                    <?php
                        // Get user account logs
                        $limit = 18;
                        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

                        if ($page < 1) $page = 1;

                        $offset = ($page - 1) * $limit;

                        $stmtLockerLogs = $conn_local->prepare("CALL getLockerLogs(?, ?)");
                        $stmtLockerLogs->bind_param("ii", $limit, $offset);

                        $stmtLockerLogs->execute();
                        $userLockerLogsResultSet = $stmtLockerLogs->get_result();
                        $stmtLockerLogs->close();

                        $conn_local->next_result();
                    ?>

                    <div class="offcanvas-header">
                        <h3 class="offcanvas-title">Locker Logs</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                    </div>

                    <div class="offcanvas-body">
                        <div class="table-container">
                            <?php if ($userLockerLogsResultSet->num_rows > 0): ?>
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th>Log ID</th>
                                            <th>Timestamp</th>
                                            <th>Action</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php while ($lockerLogsRow = $userLockerLogsResultSet->fetch_assoc()) { ?>
                                            <tr>
                                                <td data-label="Log ID"><?= $lockerLogsRow['id'] ?></td>
                                                <td data-label="Timestamp"><?= $lockerLogsRow['created_at'] ?></td>
                                                <td data-label="Action"><?= $lockerLogsRow['action'] ?></td>
                                                <td data-label="Description"><?= $lockerLogsRow['description'] ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div id="empty">
                                    <i class="fa-solid fa-ban"></i>
                                    <small>No locker logs yet</small>
                                    <small>Try to reload page</small>
                                </div>
                            <?php endif; ?>
                        </div>

                        <ul class="pagination">
                            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                <?php if ($page > 1): ?>
                                    <a class="page-link" href="?page=<?= $page - 1 ?>#lockerLogsOffcanvas">Previous</a>
                                <?php else: ?>
                                    <span class="page-link">Previous</span>
                                <?php endif; ?>
                            </li>

                            <li class="page-item active">
                                <span class="page-link"><?= $page ?></span>
                            </li>

                            <li class="page-item <?= (mysqli_num_rows($userLockerLogsResultSet) < $limit) ? 'disabled' : '' ?>">
                                <?php if (mysqli_num_rows($userLockerLogsResultSet) == $limit): ?>
                                    <a class="page-link" href="?page=<?= $page + 1 ?>#lockerLogsOffcanvas">Next</a>
                                <?php else: ?>
                                    <span class="page-link">Next</span>
                                <?php endif; ?>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Lockers header -->
                <header>
                    <form method="GET">
                        <div class="search-group">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="search"
                                name="searchLocation"
                                placeholder="Search locker locations..."
                                value="<?= htmlspecialchars($_GET['searchLocation'] ?? '') ?>">
                        </div>

                        <div class="filter-group">
                            <i class="fa-solid fa-filter"></i>
                            <select name="filterLocation" onchange="this.form.submit()">
                                <option value="">All</option>
                                <option value="a-z" <?= (($_GET['filterLocation'] ?? '') === 'a-z') ? 'selected' : '' ?>>A - Z</option>
                                <option value="z-a" <?= (($_GET['filterLocation'] ?? '') === 'z-a') ? 'selected' : '' ?>>Z - A</option>
                                <option value="newest" <?= (($_GET['filterLocation'] ?? '') === 'newest') ? 'selected' : '' ?>>Newest</option>
                                <option value="oldest" <?= (($_GET['filterLocation'] ?? '') === 'oldest') ? 'selected' : '' ?>>Oldest</option>
                            </select>
                        </div>

                        <button type="submit" hidden></button>
                    </form>
                </header>
                
                <!-- Lockers -->
                <div class="table-container">
                    <?php
                        // Get locker locations
                        $search = trim($_GET['searchLocation'] ?? '');
                        $filter = strtolower($_GET['filterLocation'] ?? '');

                        if (!empty($search) || !empty($filter)) {
                            // Use search and filter
                            $stmtLockerLocation = $conn_local->prepare("CALL getSearchFilterLockerLocation(?, ?)");
                            $stmtLockerLocation->bind_param("ss", $search, $filter);
                        } else {
                            // Use raw
                            $stmtLockerLocation = $conn_local->prepare("CALL getLockerLocations()");
                        }

                        $stmtLockerLocation->execute();
                        $lockerLocationResultSet = $stmtLockerLocation->get_result();
                        $stmtLockerLocation->close();

                        $conn_local->next_result();
                        $conn_local->store_result();
                    ?>

                    <?php if ($lockerLocationResultSet->num_rows > 0): ?>
                        <?php while ($lockerLocationRow = $lockerLocationResultSet->fetch_assoc()) { ?>
                            <div class="location-slot-section">
                                <div class="location-header">
                                    <h3><?= $lockerLocationRow['location'] ?></h3>

                                    <div class="action-buttons">
                                        <button type="button"
                                            class="sm-btn primary-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#addLockerModal<?= $lockerLocationRow['id'] ?>">
                                            <i class="fa-solid fa-plus"></i>
                                            Add locker
                                        </button>
                                    </div>
                                </div>

                                <!-- Add locker modal (per location) -->
                                <div class="modal fade primary-modal" id="addLockerModal<?= $lockerLocationRow['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">

                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    <i class="fa-solid fa-plus"></i>
                                                    Add Locker - <?= $lockerLocationRow['location'] ?>
                                                </h5>
                                            </div>

                                            <div class="modal-body">
                                                <?php
                                                    // get sizes only
                                                    $stmtLockerSizes = $conn_local->query("CALL getLockerSizes()");
                                                    $conn_local->next_result();
                                                ?>

                                                <form method="POST" action="../app/locker/add_locker.php">
                                                    <input type="hidden" name="location_id" value="<?= $lockerLocationRow['id'] ?>">

                                                    <div class="form-group">
                                                        <label class="input-label">Size</label>
                                                        <div class="input-box">
                                                            <select name="size_id" required>
                                                                <option value="">Select Size</option>
                                                                <?php while($size = $stmtLockerSizes->fetch_assoc()) { ?>
                                                                    <option value="<?= $size['id'] ?>">
                                                                        <?= $size['size'] ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="input-label">Slot Number</label>
                                                        <div class="input-box">
                                                            <input type="number" name="slot_number" required>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="input-label">Start Date</label>
                                                        <div class="input-box">
                                                            <input type="date" name="start_at" required>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="input-label">End Date</label>
                                                        <div class="input-box">
                                                            <input type="date" name="end_at" required>
                                                        </div>
                                                    </div>

                                                    <div class="action-buttons">
                                                        <button type="submit" class="btn primary-btn">Add Slot</button>
                                                        <button type="button" class="btn secondary-btn" data-bs-dismiss="modal">Cancel</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php
                                    // Get lockers by location
                                    $stmtLocker = $conn_local->prepare("CALL getLockersByLocation(?)");
                                    $stmtLocker->bind_param("i", $lockerLocationRow['id']);
                                    $stmtLocker->execute();
                                    $lockerResultSet = $stmtLocker->get_result();
                                    $stmtLocker->close();

                                    $conn_local->next_result();
                                    $conn_local->store_result();
                                ?>

                                <?php if ($lockerResultSet->num_rows > 0): ?>
                                    <table class="table table-borderless">
                                        <thead>
                                            <tr>
                                                <th>Slot Number</th>
                                                <th>Size</th>
                                                <th>Price</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php while ($lockerRow = $lockerResultSet->fetch_assoc()) { ?>
                                                <tr>
                                                    <td data-label="Slot Number"><?= $lockerRow['slot_number'] ?></td>
                                                    <td data-label="Size"><?= $lockerRow['size'] ?></td>
                                                    <td data-label="Price">&#8369;<?= $lockerRow['price'] ?></td>
                                                    <td data-label="Start Date"><?= $lockerRow['start_at'] ?></td>
                                                    <td data-label="End Date"><?= $lockerRow['end_at'] ?></td>
                                                    <td data-label="Status"><?= $lockerRow['status'] ?></td>

                                                    <td data-label="Action">
                                                        <div class="action-buttons">
                                                            <button type="button"
                                                                class="sm-btn primary-btn"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#updateLockerSlotModal<?= $lockerRow['id'] ?>">
                                                                <i class="fa-solid fa-pen"></i>
                                                                Edit
                                                            </button>

                                                            <button type="button"
                                                                class="sm-btn danger-btn"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#deleteLockerSlotConfirmationModal<?= $lockerRow['id'] ?>">
                                                                <i class="fa-solid fa-trash"></i>
                                                                Delete
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- Update locker modal -->
                                                <div class="modal fade primary-modal" id="updateLockerSlotModal<?= $lockerRow['id'] ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">

                                                            <div class="modal-header">
                                                                <h5 class="modal-title">
                                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                                    Update Locker Slot
                                                                </h5>
                                                            </div>

                                                            <div class="modal-body">
                                                                <form method="POST" action="../app/locker/update_locker.php">
                                                                    <input type="hidden" name="id" value="<?= $lockerRow['id'] ?>">

                                                                    <div class="form-group">
                                                                        <label class="input-label">Slot Number</label>
                                                                        <div class="input-box">
                                                                            <input type="number" name="slot_number" value="<?= $lockerRow['slot_number'] ?>" required>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label class="input-label">Start Date</label>
                                                                        <div class="input-box">
                                                                            <input type="date" name="start_at" value="<?= date('Y-m-d', strtotime($lockerRow['start_at'])) ?>" required>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label class="input-label">End Date</label>
                                                                        <div class="input-box">
                                                                            <input type="date" name="end_at" value="<?= date('Y-m-d', strtotime($lockerRow['end_at'])) ?>" required>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label class="input-label">Status</label>
                                                                        <div class="input-box">
                                                                            <select name="status">
                                                                                <option value="Available" <?= $lockerRow['status'] == 'Available' ? 'selected' : '' ?>>Available</option>
                                                                                <option value="Occupied" <?= $lockerRow['status'] == 'Occupied' ? 'selected' : '' ?>>Occupied</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    <div class="action-buttons">
                                                                        <button type="submit" class="btn primary-btn">Save Changes</button>
                                                                        <button type="button" class="btn secondary-btn" data-bs-dismiss="modal">Cancel</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Delete locker confirmation modal -->
                                                <div class="modal fade danger-modal" id="deleteLockerSlotConfirmationModal<?= $lockerRow['id'] ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-body">
                                                                <div class="message">
                                                                    <p><i class="fa-solid fa-circle-exclamation"></i></p>
                                                                    <h5>Delete</h5>
                                                                    <p>Are you sure you want to delete <span>slot <?= $lockerRow['slot_number'] ?></span>?</p>
                                                                </div>

                                                                <div class="action-buttons">
                                                                    <button type="button" class="btn secondary-btn" data-bs-dismiss="modal">
                                                                        Cancel
                                                                    </button>

                                                                    <form method="POST" action="../app/locker/delete_locker.php">
                                                                        <input type="hidden" name="id" value="<?= $lockerRow['id'] ?>">

                                                                        <button type="submit" class="btn primary-btn">
                                                                            Yes, Delete
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <div id="empty">
                                        <i class="fa-solid fa-ban"></i>
                                        <small>No slots yet</small>
                                        <small>Try to reload page</small>
                                    </div>
                                <?php endif; ?>
                            </div>                        
                        <?php } ?>
                    <?php else: ?>
                        <div id="empty">
                            <i class="fa-solid fa-ban"></i>
                            <small>No locker location yet</small>
                            <small>Try to reload page</small>
                        </div>
                    <?php endif; ?>
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
