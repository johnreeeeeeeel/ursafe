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

    <title>UrSafe - Applications</title>

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
                        <a class="link" href="lockers.php">
                            <i class="fa-solid fa-vault"></i>
                            Lockers
                        </a>
                    </li>

                    <li>
                        <a class="link active" href="applications.php">
                            <i class="fa-solid fa-file-lines"></i>
                            Applications
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
                    <a class="link" href="lockers.php">
                        <i class="fa-solid fa-vault"></i>
                        Lockers
                    </a>
                </li>

                <li>
                    <a class="link active" href="applications.php">
                        <i class="fa-solid fa-file-lines"></i>
                        Applications
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
                <h1 class="page-title">Applications</h1> 
            </div>
        </header>

        <div class="content">
            <div id="lockers">
                <!-- Applications -->
                <?php
                    $activeApplicationsTab = $_GET['tab'] ?? 'pendingApplicationsTab';
                ?>

                <div class="locker-tabs">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link <?= ($_GET['tab'] ?? 'pendingApplicationsTab') === 'pendingApplicationsTab' ? 'active' : '' ?>" href="?tab=pendingApplicationsTab">
                            Pending
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link <?= ($_GET['tab'] ?? '') === 'acceptedApplicationsTab' ? 'active' : '' ?>" href="?tab=acceptedApplicationsTab">
                                Accepted
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link <?= ($_GET['tab'] ?? '') === 'endedApplicationsTab' ? 'active' : '' ?>" href="?tab=endedApplicationsTab">
                                Ended
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link <?= ($_GET['tab'] ?? '') === 'historyApplicationsTab' ? 'active' : '' ?>" href="?tab=historyApplicationsTab">
                                History
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Pending applications -->
                        <?php if ($activeApplicationsTab === 'pendingApplicationsTab'): ?>
                            <div class="tab-pane fade <?= ($_GET['tab'] ?? 'pendingApplicationsTab') === 'pendingApplicationsTab' ? 'show active' : '' ?>" id="pendingApplicationsTab">
                                <?php
                                    // Pending locker application
                                    $limit = 12;
                                    $pendingPage = isset($_GET['pendingPage']) ? (int)$_GET['pendingPage'] : 1;
                                    if ($pendingPage < 1) $pendingPage = 1;

                                    $offset = ($pendingPage - 1) * $limit;

                                    $search = trim($_GET['searchPendingApplication'] ?? '');
                                    $filter = strtolower($_GET['filterPendingApplication'] ?? '');

                                    $isSearching = !empty($search);
                                    $isFiltering = ($filter !== '');
                                    $isSearchFilterMode = $isSearching || $isFiltering;

                                    if ($isSearchFilterMode) {
                                        $stmtPending = $conn_local->prepare("CALL getSearchFilterPendingLockerApplications(?, ?, ?, ?)");
                                        $stmtPending->bind_param("ssii", $search, $filter, $limit, $offset);
                                    } else {
                                        $stmtPending = $conn_local->prepare("CALL getPendingLockerApplications(?, ?)");
                                        $stmtPending->bind_param("ii", $limit, $offset);
                                    }

                                    $stmtPending->execute();

                                    $pendingResultSet = $stmtPending->get_result();

                                    $stmtPending->next_result();
                                    $totalPendingRow = $stmtPending->get_result()->fetch_assoc()['pendingTotal'];

                                    $totalPages = ceil($totalPendingRow / $limit);

                                    $stmtPending->close();

                                    while ($conn_local->next_result()) {
                                        $conn_local->store_result();
                                    }
                                ?>

                                <header class="searchFilter">
                                    <form method="GET">
                                        <input type="hidden" name="tab" value="pendingApplicationsTab">

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
                                                        <h5>Slot <?= $row['slot_number'] ?></h5>
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
                                                            <p>Academic Year: <?= $row['academic_year'] ?></p>
                                                            <p>Semester: <?= $row['semester'] ?></p>
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
                                                            <p>Payment: <?= $row['payment'] ?></p>
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

                                        <ul class="pagination">
                                            <li class="page-item <?= ($pendingPage <= 1) ? 'disabled' : '' ?>">
                                                <?php if ($pendingPage > 1): ?>
                                                    <a class="page-link"
                                                        href="?tab=pendingApplicationsTab&pendingPage=<?= $pendingPage - 1 ?>&searchPendingApplication=<?= urlencode($_GET['searchPendingApplication'] ?? '') ?>&filterPendingApplication=<?= urlencode($_GET['filterPendingApplication'] ?? '') ?>">
                                                        Previous
                                                    </a>
                                                <?php else: ?>
                                                    <span class="page-link">Previous</span>
                                                <?php endif; ?>
                                            </li>

                                            <li class="page-item active">
                                                <span class="page-link"><?= $pendingPage ?></span>
                                            </li>

                                            <li class="page-item <?= ($pendingPage >= $totalPages) ? 'disabled' : '' ?>">
                                                <?php if (mysqli_num_rows($pendingResultSet) == $limit): ?>
                                                    <a class="page-link"
                                                        href="?tab=pendingApplicationsTab&pendingPage=<?= $pendingPage + 1 ?>&searchPendingApplication=<?= urlencode($_GET['searchPendingApplication'] ?? '') ?>&filterPendingApplication=<?= urlencode($_GET['filterPendingApplication'] ?? '') ?>#lockerPendingApplicationOffcanvas">
                                                        Next
                                                    </a>
                                                <?php else: ?>
                                                    <span class="page-link">Next</span>
                                                <?php endif; ?>
                                            </li>
                                        </ul>
                                    <?php else: ?>
                                        <div id="empty">
                                            <i class="fa-solid fa-ban"></i>
                                            <small>No pending application yet</small>
                                            <small>Try to <a href="javascript:location.reload();"><i class="fa-solid fa-arrows-rotate"></i> reload</a> page</small>
                                        </div>
                                    <?php endif; ?>
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
                            </div>
                        <?php endif; ?>

                        <!-- Accepted applications -->
                        <?php if ($activeApplicationsTab === 'acceptedApplicationsTab'): ?>
                            <div class="tab-pane fade <?= ($_GET['tab'] ?? 'acceptedApplicationsTab') === 'acceptedApplicationsTab' ? 'show active' : '' ?>" id="acceptedApplicationsTab">
                                <?php
                                    // Accepted locker application
                                    $limit = 12;
                                    $acceptedPage = isset($_GET['acceptedPage']) ? (int)$_GET['acceptedPage'] : 1;

                                    if ($acceptedPage < 1) $acceptedPage = 1;

                                    $offset = ($acceptedPage - 1) * $limit;
                                    
                                    $search = trim($_GET['searchAcceptedApplication'] ?? '');
                                    $filter = strtolower($_GET['filterAcceptedApplication'] ?? '');

                                    $isSearching = !empty($search);
                                    $isFiltering = ($filter !== '');
                                    $isSearchFilterMode = $isSearching || $isFiltering;

                                    if ($isSearchFilterMode) {
                                        $stmtAccepted = $conn_local->prepare("CALL getSearchFilterAcceptedLockerApplications(?, ?, ?, ?)");
                                        $stmtAccepted->bind_param("ssii", $search, $filter, $limit, $offset);
                                    } else {
                                        $stmtAccepted = $conn_local->prepare("CALL getAcceptedLockerApplications(?, ?)");
                                        $stmtAccepted->bind_param("ii", $limit, $offset);
                                    }

                                    $stmtAccepted->execute();

                                    $acceptedResultSet = $stmtAccepted->get_result();

                                    $stmtAccepted->next_result();
                                    $totalAcceptedRow = $stmtAccepted->get_result()->fetch_assoc()['acceptedTotal'];

                                    $totalPages = ceil($totalAcceptedRow / $limit);

                                    $stmtAccepted->close();

                                    while ($conn_local->next_result()) {
                                        $conn_local->store_result();
                                    }
                                ?>

                                <header class="searchFilter">
                                    <form method="GET">
                                        <input type="hidden" name="tab" value="acceptedApplicationsTab">

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
                                                        <h5>Slot <?= $row['slot_number'] ?></h5>
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
                                                            <p>Academic Year: <?= $row['academic_year'] ?></p>
                                                            <p>Semester: <?= $row['semester'] ?></p>
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
                                                            <p>Payment: <?= $row['payment'] ?></p>
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
                                        <ul class="pagination">
                                            <li class="page-item <?= ($acceptedPage <= 1) ? 'disabled' : '' ?>">
                                                <?php if ($acceptedPage > 1): ?>
                                                    <a class="page-link"
                                                    href="?tab=acceptedApplicationsTab&acceptedPage=<?= $acceptedPage - 1 ?>&searchAcceptedApplication=<?= urlencode($_GET['searchAcceptedApplication'] ?? '') ?>&filterAcceptedApplication=<?= urlencode($_GET['filterAcceptedApplication'] ?? '') ?>">
                                                        Previous
                                                    </a>
                                                <?php else: ?>
                                                    <span class="page-link">Previous</span>
                                                <?php endif; ?>
                                            </li>

                                            <li class="page-item active">
                                                <span class="page-link"><?= $acceptedPage ?></span>
                                            </li>

                                            <li class="page-item <?= ($acceptedPage >= $totalPages) ? 'disabled' : '' ?>">
                                                <?php if (mysqli_num_rows($acceptedResultSet) == $limit): ?>
                                                    <a class="page-link"
                                                    href="?tab=acceptedApplicationsTab&acceptedPage=<?= $acceptedPage + 1 ?>&searchAcceptedApplication=<?= urlencode($_GET['searchAcceptedApplication'] ?? '') ?>&filterAcceptedApplication=<?= urlencode($_GET['filterAcceptedApplication'] ?? '') ?>">
                                                        Next
                                                    </a>
                                                <?php else: ?>
                                                    <span class="page-link">Next</span>
                                                <?php endif; ?>
                                            </li>
                                        </ul>
                                    <?php else: ?>
                                        <div id="empty">
                                            <i class="fa-solid fa-ban"></i>
                                            <small>No accepted application yet</small>
                                            <small>Try to <a href="javascript:location.reload();"><i class="fa-solid fa-arrows-rotate"></i> reload</a> page</small>
                                        </div>
                                    <?php endif; ?>
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
                            </div>
                        <?php endif; ?>

                        <!-- Ended applications -->
                        <?php if ($activeApplicationsTab === 'endedApplicationsTab'): ?>
                            <div class="tab-pane fade <?= ($_GET['tab'] ?? 'endedApplicationsTab') === 'endedApplicationsTab' ? 'show active' : '' ?>" id="endedApplicationsTab">
                                <?php
                                    // Ended locker application
                                    $limit = 12;
                                    $endedPage = isset($_GET['endedPage']) ? (int)$_GET['endedPage'] : 1;

                                    if ($endedPage < 1) $endedPage = 1;

                                    $offset = ($endedPage - 1) * $limit;
                                    
                                    $search = trim($_GET['searchEndedApplication'] ?? '');
                                    $filter = strtolower($_GET['filterEndedApplication'] ?? '');

                                    $isSearching = !empty($search);
                                    $isFiltering = ($filter !== '');
                                    $isSearchFilterMode = $isSearching || $isFiltering;

                                    if ($isSearchFilterMode) {
                                        $stmtEnded = $conn_local->prepare("CALL getSearchFilterEndedLockerApplications(?, ?, ?, ?)");
                                        $stmtEnded->bind_param("ssii", $search, $filter, $limit, $offset);
                                    } else {
                                        $stmtEnded = $conn_local->prepare("CALL getEndedLockerApplications(?, ?)");
                                        $stmtEnded->bind_param("ii", $limit, $offset);
                                    }

                                    $stmtEnded->execute();
                                    $endedResultSet = $stmtEnded->get_result();

                                    $stmtEnded->next_result();
                                    $totalEndedRow = $stmtEnded->get_result()->fetch_assoc()['endedTotal'];

                                    $totalPages = ceil($totalEndedRow / $limit);

                                    $stmtEnded->close();

                                    while ($conn_local->next_result()) {
                                        $conn_local->store_result();
                                    }
                                ?>

                                <header class="searchFilter">
                                    <form method="GET">
                                        <input type="hidden" name="tab" value="endedApplicationsTab">

                                        <div class="search-group">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                            <input type="search"
                                                name="searchEndedApplication"
                                                placeholder="Search..."
                                                value="<?= htmlspecialchars($_GET['searchEndedApplication'] ?? '') ?>">
                                        </div>

                                        <div class="filter-group">
                                            <i class="fa-solid fa-filter"></i>
                                            <select name="filterEndedApplication" onchange="this.form.submit()">
                                                <option value="">All</option>
                                                <option value="newest" <?= (($_GET['filterEndedApplication'] ?? '') === 'newest') ? 'selected' : '' ?>>Newest</option>
                                                <option value="oldest" <?= (($_GET['filterEndedApplication'] ?? '') === 'oldest') ? 'selected' : '' ?>>Oldest</option>
                                            </select>
                                        </div>

                                        <button type="submit" hidden></button>
                                    </form>
                                </header>

                                <div class="card-container">
                                    <?php if ($endedResultSet->num_rows > 0): ?>
                                        <div class="cards">
                                            <?php while ($row = $endedResultSet->fetch_assoc()) { ?>
                                                <div class="card accepted">
                                                    <div class="card-header">
                                                        <h5>Slot <?= $row['slot_number'] ?></h5>
                                                        <p><?= $row['location'] ?></p>

                                                        <span class="badge rounded-pill ended-badge"><?= $row['status'] ?></span>
                                                    </div>

                                                    <div class="card-body">
                                                        <div class="locker-details">
                                                            <p class="label">
                                                                <i class="fa-solid fa-vault"></i>
                                                                Locker Details
                                                            </p>

                                                            <p>Size: <?= $row['size'] ?></p>
                                                            <p>Price: &#8369;<?= $row['price'] ?></p>
                                                            <p>Academic Year: <?= $row['academic_year'] ?></p>
                                                            <p>Semester: <?= $row['semester'] ?></p>
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
                                                            <p>Payment: <?= $row['payment'] ?></p>
                                                            <p>Ended on: <?= $row['updated_at'] ?></p>
                                                        </div>
                                                    </div>

                                                    <div class="card-footer">
                                                        <div class="action-buttons">
                                                            <button class="sm-btn secondary-btn"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#paidLockerApplicationModal<?= $row['application_id'] ?>">
                                                                <i class="fa-brands fa-cash-app"></i>
                                                                Paid
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <ul class="pagination">
                                            <li class="page-item <?= ($endedPage <= 1) ? 'disabled' : '' ?>">
                                                <?php if ($endedPage > 1): ?>
                                                    <a class="page-link"
                                                    href="?tab=endedApplicationsTab&endedPage=<?= $endedPage - 1 ?>&searchEndedApplication=<?= urlencode($_GET['searchEndedApplication'] ?? '') ?>&filterEndedApplication=<?= urlencode($_GET['filterEndedApplication'] ?? '') ?>">
                                                        Previous
                                                    </a>
                                                <?php else: ?>
                                                    <span class="page-link">Previous</span>
                                                <?php endif; ?>
                                            </li>

                                            <li class="page-item active">
                                                <span class="page-link"><?= $endedPage ?></span>
                                            </li>

                                            <li class="page-item <?= ($endedPage >= $totalPages) ? 'disabled' : '' ?>">
                                                <?php if (mysqli_num_rows($endedResultSet) == $limit): ?>
                                                    <a class="page-link"
                                                    href="?tab=endedApplicationsTab&endedPage=<?= $endedPage + 1 ?>&searchEndedApplication=<?= urlencode($_GET['searchEndedApplication'] ?? '') ?>&filterEndedApplication=<?= urlencode($_GET['filterEndedApplication'] ?? '') ?>#lockerEndedApplicationOffcanvas">
                                                        Next
                                                    </a>
                                                <?php else: ?>
                                                    <span class="page-link">Next</span>
                                                <?php endif; ?>
                                            </li>
                                        </ul>
                                    <?php else: ?>
                                        <div id="empty">
                                            <i class="fa-solid fa-ban"></i>
                                            <small>No ended application yet</small>
                                            <small>Try to <a href="javascript:location.reload();"><i class="fa-solid fa-arrows-rotate"></i> reload</a> page</small>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php foreach ($endedResultSet as $row) { ?>
                                    <!-- Paid locker application modals -->
                                    <div class="modal fade success-modal" id="paidLockerApplicationModal<?= $row['application_id'] ?>" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-body">
                                                    <div class="message">
                                                        <i class="fa-brands fa-cash-app"></i>
                                                        <h5>Paid Application</h5>
                                                        <p>Are you sure you want to set paid <span>application <?= $row['application_id'] ?></span>?</p>
                                                    </div>

                                                    <div class="action-buttons">
                                                        <button type="button" class="btn secondary-btn" data-bs-dismiss="modal">
                                                            Cancel
                                                        </button>

                                                        <form method="POST" action="../app/locker/paid_locker_application.php">
                                                            <input type="hidden" name="id" value="<?= $row['application_id'] ?>">

                                                            <button type="submit" class="btn primary-btn">
                                                                Yes, Paid
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php endif; ?>

                        <!-- History applications -->
                        <?php if ($activeApplicationsTab === 'historyApplicationsTab'): ?>
                            <div class="tab-pane fade <?= ($_GET['tab'] ?? 'historyApplicationsTab') === 'historyApplicationsTab' ? 'show active' : '' ?>" id="historyApplicationsTab">
                                <?php
                                    // Locker application history 
                                    $limit = 12;
                                    $historyPage = isset($_GET['historyPage']) ? (int)$_GET['historyPage'] : 1;

                                    if ($historyPage < 1) $historyPage = 1;

                                    $offset = ($historyPage - 1) * $limit;

                                    $search = trim($_GET['searchApplicationHistory'] ?? '');
                                    $filter = strtolower($_GET['filterApplicationHistory'] ?? '');

                                    $isSearching = !empty($search);
                                    $isFiltering = ($filter !== '');
                                    $isSearchFilterMode = $isSearching || $isFiltering;

                                    if ($isSearchFilterMode) {
                                        $stmtHistory = $conn_local->prepare("CALL getSearchFilterLockerApplicationHistory(?, ?, ?, ?)");
                                        $stmtHistory->bind_param("ssii", $search, $filter, $limit, $offset);
                                    } else {
                                        $stmtHistory = $conn_local->prepare("CALL getLockerApplicationHistory(?, ?)");
                                        $stmtHistory->bind_param("ii", $limit, $offset);
                                    }

                                    $stmtHistory->execute();

                                    $historyResultSet = $stmtHistory->get_result();

                                    $stmtHistory->next_result();
                                    $totalHistoryRow = $stmtHistory->get_result()->fetch_assoc()['historyTotal'];

                                    $totalPages = ceil($totalHistoryRow / $limit);

                                    $stmtHistory->close();

                                    while ($conn_local->next_result()) {
                                        $conn_local->store_result();
                                    }
                                ?>
                                <header class="searchFilter">
                                    <form method="GET">
                                        <input type="hidden" name="tab" value="historyApplicationsTab">

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

                                <div class="table-container">
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
                                                    <th>Academic Year - Semester</th>
                                                    <th>Start On</th>
                                                    <th>End On</th>
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
                                                        
                                                        <td data-label="Academic Year - Semester">
                                                            <?= $row['academic_year'] ?> - <?= $row['semester'] ?>
                                                        </td>

                                                        <td data-label="Start On"><?= $row['start_at'] ?></td>
                                                        <td data-label="End On"><?= $row['end_at'] ?></td>

                                                        <td data-label="Status">
                                                            <?php if ($row['status'] == 'Revoked') { ?>
                                                                <span class="badge rounded-pill revoked-badge">Revoked</span>
                                                            <?php } elseif ($row['status'] == 'Cancelled') { ?>
                                                                <span class="badge rounded-pill cancelled-badge">Cancelled</span>
                                                            <?php } elseif ($row['status'] == 'Rejected') { ?>
                                                                <span class="badge rounded-pill rejected-badge">Rejected</span>
                                                            <?php } elseif ($row['payment'] == 'Paid') { ?>
                                                                <span class="badge rounded-pill ended-badge">Ended - Paid</span>
                                                            <?php } elseif ($row['payment'] == 'Unpaid') { ?>
                                                                <span class="badge rounded-pill ended-badge">Ended - Unpaid</span>
                                                            <?php } ?>
                                                        </td>
                                                        
                                                        <td data-label="Date"><?= $row['updated_at'] ?></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>

                                        <ul class="pagination">
                                            <li class="page-item <?= ($historyPage <= 1) ? 'disabled' : '' ?>">
                                                <?php if ($historyPage > 1): ?>
                                                    <a class="page-link"
                                                    href="?tab=historyApplicationsTab&historyPage=<?= $historyPage - 1 ?>&searchApplicationHistory=<?= urlencode($_GET['searchApplicationHistory'] ?? '') ?>&filterApplicationHistory=<?= urlencode($_GET['filterApplicationHistory'] ?? '') ?>">
                                                        Previous
                                                    </a>
                                                <?php else: ?>
                                                    <span class="page-link">Previous</span>
                                                <?php endif; ?>
                                            </li>

                                            <li class="page-item active">
                                                <span class="page-link"><?= $historyPage ?></span>
                                            </li>

                                            <li class="page-item <?= ($historyPage >= $totalPages) ? 'disabled' : '' ?>">
                                                <?php if (mysqli_num_rows($historyResultSet) == $limit): ?>
                                                    <a class="page-link"
                                                    href="?tab=historyApplicationsTab&historyPage=<?= $historyPage + 1 ?>&searchApplicationHistory=<?= urlencode($_GET['searchApplicationHistory'] ?? '') ?>&filterApplicationHistory=<?= urlencode($_GET['filterApplicationHistory'] ?? '') ?>#lockerApplicationHistoryOffcanvas">
                                                        Next
                                                    </a>
                                                <?php else: ?>
                                                    <span class="page-link">Next</span>
                                                <?php endif; ?>
                                            </li>
                                        </ul>
                                    <?php else: ?>
                                        <div id="empty">
                                            <i class="fa-solid fa-ban"></i>
                                            <small>No application history yet</small>
                                            <small>Try to <a href="javascript:location.reload();"><i class="fa-solid fa-arrows-rotate"></i> reload</a> page</small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
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
