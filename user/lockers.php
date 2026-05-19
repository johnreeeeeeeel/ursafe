<?php
session_start();
require '../app/db_connection.php';

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
                        <a class="link active" href="lockers.php">
                            <i class="fa-solid fa-vault"></i>
                            Lockers
                        </a>
                    </li>       
                </div>

                <div class="bottom-nav">
                    <li>
                        <a class="link" href="profile.php">
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
                    <a class="link active" href="lockers.php">
                        <i class="fa-solid fa-vault"></i>
                        Lockers
                    </a>
                </li>
            </div>

            <div class="bottom-nav">
                <li>
                    <a class="link" href="profile.php">
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
                <h1 class="page-title">Lockers</h1> 
            </div>

            <div class="right">
                <div class="dropdown">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                        View
                    </button>
                    
                    <ul class="dropdown-menu"> 
                        <li>
                            <button type="button" class="dropdown-item" data-bs-toggle="offcanvas" data-bs-target="#myLockerApplicationOffcanvas" onclick="window.location.hash='myLockerApplicationOffcanvas';">
                                My Locker Applications
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <div class="content">
            <div id="lockers">
                <!-- Offcanvas for my locker application -->
                <div class="offcanvas offcanvas-end" id="myLockerApplicationOffcanvas">
                    <div class="offcanvas-header">
                        <h3 class="offcanvas-title">My Locker Applications</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                    </div>

                    <div class="offcanvas-body">
                        <div class="locker-applications-container">
                            <!-- Accepted applications -->
                            <div class="card-container">
                                <?php
                                    // Accepted locker application
                                    $limit = 4;
                                    $acceptedPage = isset($_GET['acceptedPage']) ? (int)$_GET['acceptedPage'] : 1;
                                    if ($acceptedPage < 1) $acceptedPage = 1;

                                    $offset = ($acceptedPage - 1) * $limit;

                                    $stmtAccepted = $conn_local->prepare("CALL getMyAcceptedLockerApplications(?, ?, ?)");
                                    $stmtAccepted->bind_param("sii", $id, $limit, $offset);

                                    $stmtAccepted->execute();

                                    $acceptedResultSet = $stmtAccepted->get_result();

                                    $stmtAccepted->next_result();
                                    $totalAcceptedRow = $stmtAccepted->get_result()->fetch_assoc()['myAcceptedTotal'];

                                    $totalPagesAccepted = ceil($totalAcceptedRow / $limit);

                                    $stmtAccepted->close();

                                    while ($conn_local->next_result()) {
                                        $conn_local->store_result();
                                    }
                                ?>

                                <h3>Accepted Applications</h3>

                                <?php if ($acceptedResultSet->num_rows > 0): ?>
                                    <div class="cards">
                                        <?php while ($row = $acceptedResultSet->fetch_assoc()) { ?>
                                            <div class="card accepted">
                                                <div class="card-header">
                                                    <h4>Slot <?= $row['slot_number'] ?></h4>
                                                    <p><?= $row['location'] ?></p>

                                                    <span class="badge rounded-pill accepted-badge">Accepted</span>
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

                                                    <div class="application-details">
                                                        <p class="label">
                                                            <i class="fa-brands fa-jxl"></i>
                                                            Application Details
                                                        </p>

                                                        <p>Application ID: #<?= $row['application_id'] ?></p>
                                                        <p>Payment: <?= $row['payment'] ?></p>
                                                        <p>Accepted on: <?= $row['updated_at'] ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <ul class="pagination">
                                        <li class="page-item <?= ($acceptedPage <= 1) ? 'disabled' : '' ?>">
                                            <a class="page-link"
                                            href="?acceptedPage=<?= $acceptedPage - 1 ?>#myLockerApplicationOffcanvas">
                                                Previous
                                            </a>
                                        </li>

                                        <li class="page-item active">
                                            <span class="page-link"><?= $acceptedPage ?></span>
                                        </li>

                                        <li class="page-item <?= ($acceptedPage >= $totalPagesAccepted) ? 'disabled' : '' ?>">
                                            <a class="page-link"
                                            href="?acceptedPage=<?= $acceptedPage + 1 ?>#myLockerApplicationOffcanvas">
                                                Next
                                            </a>
                                        </li>
                                    </ul>
                                <?php else: ?>
                                    <div id="empty">
                                        <i class="fa-solid fa-ban"></i>
                                        <small>No accepted application yet</small>
                                        <small>Try to reload page</small>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Pending applications -->
                            <div class="card-container">
                                <?php
                                    // Pending locker application
                                    $limit = 4;
                                    $pendingPage = isset($_GET['pendingPage']) ? (int)$_GET['pendingPage'] : 1;
                                    if ($pendingPage < 1) $pendingPage = 1;

                                    $offset = ($pendingPage - 1) * $limit;

                                    $stmtPending = $conn_local->prepare("CALL getMyPendingLockerApplications(?, ?, ?)");
                                    $stmtPending->bind_param("sii", $id, $limit, $offset);

                                    $stmtPending->execute();

                                    $pendingResultSet = $stmtPending->get_result();

                                    $stmtPending->next_result();
                                    $totalPendingRow = $stmtPending->get_result()->fetch_assoc()['myPendingTotal'];

                                    $totalPagesPending = ceil($totalPendingRow / $limit);

                                    $stmtPending->close();

                                    while ($conn_local->next_result()) {
                                        $conn_local->store_result();
                                    }
                                ?>

                                <h3>Pending Applications</h3>

                                <?php if ($pendingResultSet->num_rows > 0): ?>
                                    <div class="cards">
                                        <?php while ($row = $pendingResultSet->fetch_assoc()) { ?>
                                            <div class="card pending">
                                                <div class="card-header">
                                                    <h4>Slot <?= $row['slot_number'] ?></h4>
                                                    <p><?= $row['location'] ?></p>

                                                    <span class="badge rounded-pill pending-badge">Pending</span>
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

                                                    <div class="application-details">
                                                        <p class="label">
                                                            <i class="fa-brands fa-jxl"></i>
                                                            Application Details
                                                        </p>

                                                        <p>Application ID: #<?= $row['application_id'] ?></p>
                                                        <p>Payment: <?= $row['payment'] ?></p>
                                                        <p>Applied on: <?= $row['created_at'] ?></p>
                                                    </div>
                                                </div>

                                                <div class="card-footer">
                                                    <div class="action-buttons">
                                                        <button class="sm-btn danger-btn"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#cancelApplicationModal<?= $row['application_id'] ?>">
                                                            <i class="fa-solid fa-xmark"></i>
                                                            Cancel Application
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <ul class="pagination">
                                        <li class="page-item <?= ($pendingPage <= 1) ? 'disabled' : '' ?>">
                                            <a class="page-link"
                                            href="?pendingPage=<?= $pendingPage - 1 ?>#myLockerApplicationOffcanvas">
                                                Previous
                                            </a>
                                        </li>

                                        <li class="page-item active">
                                            <span class="page-link"><?= $pendingPage ?></span>
                                        </li>

                                        <li class="page-item <?= ($pendingPage >= $totalPagesPending) ? 'disabled' : '' ?>">
                                            <a class="page-link"
                                            href="?pendingPage=<?= $pendingPage + 1 ?>#myLockerApplicationOffcanvas">
                                                Next
                                            </a>
                                        </li>
                                    </ul>
                                <?php else: ?>
                                    <div id="empty">
                                        <i class="fa-solid fa-ban"></i>
                                        <small>No pending application yet</small>
                                        <small>Try to reload page</small>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Ended applications -->
                            <div class="card-container">
                                <?php
                                    // Ended locker application
                                    $limit = 4;
                                    $endedPage = isset($_GET['endedPage']) ? (int)$_GET['endedPage'] : 1;
                                    if ($endedPage < 1) $endedPage = 1;

                                    $offset = ($endedPage - 1) * $limit;

                                    $stmtEnded = $conn_local->prepare("CALL getMyEndedLockerApplications(?, ?, ?)");
                                    $stmtEnded->bind_param("sii", $id, $limit, $offset);

                                    $stmtEnded->execute();

                                    $endedResultSet = $stmtEnded->get_result();

                                    $stmtEnded->next_result();
                                    $totalEndedRow = $stmtEnded->get_result()->fetch_assoc()['myEndedTotal'];

                                    $totalPagesAccepted = ceil($totalEndedRow / $limit);

                                    $stmtEnded->close();

                                    while ($conn_local->next_result()) {
                                        $conn_local->store_result();
                                    }
                                ?>

                                <h3>Ended Applications</h3>

                                <?php if ($endedResultSet->num_rows > 0): ?>
                                    <div class="cards">
                                        <?php while ($row = $endedResultSet->fetch_assoc()) { ?>
                                            <div class="card ended">
                                                <div class="card-header">
                                                    <h4>Slot <?= $row['slot_number'] ?></h4>
                                                    <p><?= $row['location'] ?></p>

                                                    <span class="badge rounded-pill ended-badge">Ended</span>
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

                                                    <div class="application-details">
                                                        <p class="label">
                                                            <i class="fa-brands fa-jxl"></i>
                                                            Application Details
                                                        </p>

                                                        <p>Application ID: #<?= $row['application_id'] ?></p>
                                                        <p>Payment: <?= $row['payment'] ?></p>
                                                        <p>Ended on: <?= $row['updated_at'] ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <ul class="pagination">
                                        <li class="page-item <?= ($endedPage <= 1) ? 'disabled' : '' ?>">
                                            <a class="page-link"
                                            href="?endedPage=<?= $endedPage - 1 ?>#myLockerApplicationOffcanvas">
                                                Previous
                                            </a>
                                        </li>

                                        <li class="page-item active">
                                            <span class="page-link"><?= $endedPage ?></span>
                                        </li>

                                        <li class="page-item <?= ($endedPage >= $totalPagesAccepted) ? 'disabled' : '' ?>">
                                            <a class="page-link"
                                            href="?endedPage=<?= $endedPage + 1 ?>#myLockerApplicationOffcanvas">
                                                Next
                                            </a>
                                        </li>
                                    </ul>
                                <?php else: ?>
                                    <div id="empty">
                                        <i class="fa-solid fa-ban"></i>
                                        <small>No ended application yet</small>
                                        <small>Try to reload page</small>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Application history -->
                            <div class="table-container">
                                <h3>Application History</h3>

                                <?php
                                    // Locker application history 
                                    $limit = 8;
                                    $historyPage = isset($_GET['historyPage']) ? (int)$_GET['historyPage'] : 1;
                                    if ($historyPage < 1) $historyPage = 1;

                                    $offset = ($historyPage - 1) * $limit;

                                    $stmtHistory = $conn_local->prepare("CALL getMyLockerApplicationHistory(?, ?, ?)");
                                    $stmtHistory->bind_param("sii", $id, $limit, $offset);

                                    $stmtHistory->execute();

                                    $historyResultSet = $stmtHistory->get_result();

                                    $stmtHistory->next_result();
                                    $totalHistoryRow = $stmtHistory->get_result()->fetch_assoc()['myHistoryTotal'];

                                    $totalPagesHistory = ceil($totalHistoryRow / $limit);

                                    $stmtHistory->close();

                                    while ($conn_local->next_result()) {
                                        $conn_local->store_result();
                                    }
                                ?>

                                <div class="table-container">
                                    <?php if ($historyResultSet->num_rows > 0): ?>
                                        <table class="table table-borderless">
                                                <thead>
                                                    <tr>
                                                        <th>Application ID</th>
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
                                                                <?php } elseif ($row['payment'] == 'Paid') { ?>
                                                                    <span class="badge rounded-pill ended-badge">Ended - Paid</span>
                                                                <?php } elseif ($row['payment'] == 'Unpaid') { ?>
                                                                    <span class="badge rounded-pill ended-badge">Ended - Unpaid</span>
                                                                <?php } else { ?>
                                                                    <span class="badge rounded-pill rejected-badge">Rejected</span>
                                                                <?php } ?>
                                                            </td>

                                                            <td data-label="Date"><?= $row['updated_at'] ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                        </table>
                                        <ul class="pagination">
                                            <li class="page-item <?= ($historyPage <= 1) ? 'disabled' : '' ?>">
                                                <a class="page-link"
                                                href="?historyPage=<?= $historyPage - 1 ?>#myLockerApplicationOffcanvas">
                                                    Previous
                                                </a>
                                            </li>

                                            <li class="page-item active">
                                                <span class="page-link"><?= $historyPage ?></span>
                                            </li>

                                            <li class="page-item <?= ($historyPage >= $totalPagesHistory) ? 'disabled' : '' ?>">
                                                <a class="page-link"
                                                href="?historyPage=<?= $historyPage + 1 ?>#myLockerApplicationOffcanvas">
                                                    Next
                                                </a>
                                            </li>
                                        </ul>
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
                    </div>
                </div>

                <?php foreach ($pendingResultSet as $row) { ?>
                    <div class="modal fade danger-modal" id="cancelApplicationModal<?= $row['application_id'] ?>" tabindex="-1" data-bs-backdrop="true" data-bs-keyboard="true" style="z-index: 2000;">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div class="message">
                                        <i class="fa-solid fa-circle-xmark"></i>
                                        <h5>Cancel Application</h5>
                                        <p>Are you sure you want to cancel application on <span>slot <?= $row['slot_number'] ?></span>?</p>
                                    </div>

                                    <div class="action-buttons">
                                        <button class="btn secondary-btn" data-bs-dismiss="modal">
                                            No
                                        </button>

                                        <form method="POST" action="../app/locker/cancel_locker_application.php">
                                            <input type="hidden" name="id" value="<?= $row['application_id'] ?>">
                                            <button class="btn primary-btn">Yes, Cancel</button>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                <?php } ?>

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

                        while ($conn_local->next_result()) {
                            $conn_local->store_result();
                        }
                    ?>

                    <?php if ($lockerLocationResultSet->num_rows > 0): ?>
                        <?php while($lockerLocationRow = $lockerLocationResultSet->fetch_assoc()) { ?>
                            <div class="location-slot-section">
                                <div class="location-header">
                                    <h3><?= $lockerLocationRow['location'] ?></h3>
                                </div>

                                <?php
                                    // Get lockers by location
                                    $stmtLocker = $conn_local->prepare("CALL getLockersByLocation(?)");
                                    $stmtLocker->bind_param("i", $lockerLocationRow['id']);
                                    $stmtLocker->execute();
                                    $lockerResultSet = $stmtLocker->get_result();
                                    $stmtLocker->close();

                                    while ($conn_local->next_result()) {
                                        $conn_local->store_result();
                                    }
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
                                            <?php while($lockerRow = $lockerResultSet->fetch_assoc()) { ?>
                                                <tr>
                                                <td data-label="Slot Number"><?= $lockerRow['slot_number'] ?></td>
                                                <td data-label="Size"><?= $lockerRow['size'] ?></td>
                                                <td data-label="Price">&#8369;<?= $lockerRow['price'] ?></td>
                                                <td data-label="Status"><?= $lockerRow['start_at'] ?></td>
                                                <td data-label="Status"><?= $lockerRow['end_at'] ?></td>
                                                <td data-label="Status"><?= $lockerRow['status'] ?></td>

                                                <td data-label="Action">
                                                    <div class="action-buttons">
                                                        <button 
                                                            type="button" 
                                                            class="sm-btn secondary-btn"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#applyLockerSlotModal<?= $lockerRow['id'] ?>">

                                                            <i class="fa-brands fa-jxl"></i>
                                                            Apply
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>

                                                <!-- Apply locker slot modal -->
                                                <div class="modal fade success-modal"
                                                    id="applyLockerSlotModal<?= $lockerRow['id'] ?>"
                                                    tabindex="-1">

                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">

                                                            <div class="modal-body">
                                                                <div class="message">
                                                                    <i class="fa-brands fa-jxl"></i>
                                                                    <h5>Apply</h5>
                                                                    <p>
                                                                        Are you sure you want to apply <span>slot <?= $lockerRow['slot_number'] ?></span>?
                                                                    </p>
                                                                </div>

                                                                <div class="action-buttons">
                                                                    <button type="button" class="btn secondary-btn" data-bs-dismiss="modal">
                                                                        Cancel
                                                                    </button>

                                                                    <form method="POST" action="../app/locker/apply_locker_slot.php">
                                                                        <input type="hidden" name="slot_id" value="<?= $lockerRow['id'] ?>">

                                                                        <button type="submit" class="btn primary-btn">
                                                                            Yes, Apply
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
                                        <small>No locker slots yet</small>
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