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
                        <a class="link" href="applications.php">
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
                    <a class="link active" href="lockers.php">
                        <i class="fa-solid fa-vault"></i>
                        Lockers
                    </a>
                </li>
                
                <li>
                    <a class="link" href="applications.php">
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
                <h1 class="page-title">Lockers</h1> 
            </div>

            <div class="right">
                <div class="dropdown">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                        View
                    </button>
                    
                    <ul class="dropdown-menu"> 
                        <li>
                            <button type="button" class="dropdown-item" data-bs-toggle="offcanvas" data-bs-target="#academicCalendarOffcanvas" onclick="window.location.hash='academicCalendarOffcanvas';">
                                Academic Calendar
                            </button> 
                        </li>
                        <li><hr class="dropdown-divider"></hr></li>
                        <li>
                            <button type="button" class="dropdown-item" data-bs-toggle="offcanvas" data-bs-target="#logsOffcanvas" onclick="window.location.hash='logsOffcanvas';">
                                Logs
                            </button> 
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <div class="content">
            <div id="lockers">
                <!-- Offcanvas for academic calendar -->
                <div class="offcanvas offcanvas-end" id="academicCalendarOffcanvas">
                    <?php
                        // Get academic calendar
                        $limit = 6;
                        $academicCalendarPage = isset($_GET['academicCalendarPage']) ? (int)$_GET['academicCalendarPage'] : 1;

                        if ($academicCalendarPage < 1) $academicCalendarPage = 1;

                        $offset = ($academicCalendarPage - 1) * $limit;

                        $search = trim($_GET['searchAcademicCalendar'] ?? '');
                        $filter = strtolower($_GET['filterAcademicCalendar'] ?? '');

                        $isSearching = !empty($search);
                        $isFiltering = ($filter !== '');
                        $isSearchFilterMode = $isSearching || $isFiltering;

                        if ($isSearchFilterMode) {
                            // Use search and filter
                            $stmtAcademicCalendar = $conn_local->prepare("CALL getSearchFilterAcademicCalendar(?, ?, ?, ?)");
                            $stmtAcademicCalendar->bind_param("ssii", $search, $filter, $limit, $offset);
                        } else {
                            // Use raw
                            $stmtAcademicCalendar = $conn_local->prepare("CALL getAcademicCalendar(?, ?)");
                            $stmtAcademicCalendar->bind_param("ii", $limit, $offset);
                        }

                        $stmtAcademicCalendar->execute();
                        $academicCalendarResultSet = $stmtAcademicCalendar->get_result();

                        $stmtAcademicCalendar->next_result();
                        $totalAcademicCalendarRow = $stmtAcademicCalendar->get_result()->fetch_assoc()['academicCalendarTotal'];

                        $totalAcademicCalendarPages = ceil($totalAcademicCalendarRow / $limit);

                        $stmtAcademicCalendar->close();

                        while ($conn_local->next_result()) {
                            $conn_local->store_result();
                        }
                    ?>

                    <div class="offcanvas-header">
                        <h3 class="offcanvas-title">Academic Calendar</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                    </div>

                    <div class="offcanvas-body">
                        <header class="searchFilter">
                            <form method="GET">
                                <div class="search-group">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    <input type="search"
                                        name="searchAcademicCalendar"
                                        placeholder="Search..."
                                        value="<?= htmlspecialchars($_GET['searchAcademicCalendar'] ?? '') ?>">
                                </div>

                                <div class="filter-group">
                                    <i class="fa-solid fa-filter"></i>
                                    <select name="filterAcademicCalendar" onchange="this.form.submit()">
                                        <option value="">All</option>
                                        <option value="a-z" <?= (($_GET['filterAcademicCalendar'] ?? '') === 'a-z') ? 'selected' : '' ?>>A - Z</option>
                                        <option value="z-a" <?= (($_GET['filterAcademicCalendar'] ?? '') === 'z-a') ? 'selected' : '' ?>>Z - A</option>
                                        <option value="newest" <?= (($_GET['filterAcademicCalendar'] ?? '') === 'newest') ? 'selected' : '' ?>>Newest</option>
                                        <option value="oldest" <?= (($_GET['filterAcademicCalendar'] ?? '') === 'oldest') ? 'selected' : '' ?>>Oldest</option>
                                    </select>
                                </div>

                                <button type="submit" hidden></button>
                            </form>
                        </header>

                        <div class="table-container">
                            <?php if ($academicCalendarResultSet->num_rows > 0): ?>
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th>Academic Year - Semester</th>
                                            <th>Start On</th>
                                            <th>End On</th>
                                            <th>Created At</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php while ($academicCalendarRow = $academicCalendarResultSet->fetch_assoc()) { ?>
                                            <tr>
                                                <td data-label="Academic Year - Semester"><?= $academicCalendarRow['academic_year'] ?> - <?= $academicCalendarRow['semester'] ?></td>
                                                <td data-label="Start On"><?= $academicCalendarRow['start_at'] ?></td>
                                                <td data-label="End On"><?= $academicCalendarRow['end_at'] ?></td>
                                                <td data-label="Created At"><?= $academicCalendarRow['created_at'] ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>

                                <ul class="pagination">
                                    <li class="page-item <?= ($academicCalendarPage <= 1) ? 'disabled' : '' ?>">
                                        <?php if ($academicCalendarPage > 1): ?>
                                            <a class="page-link" href="?academicCalendarPage=<?= $academicCalendarPage - 1 ?>&searchAcademicCalendar=<?= urlencode($_GET['searchAcademicCalendar'] ?? '') ?>&filterAcademicCalendar=<?= urlencode($_GET['filterAcademicCalendar'] ?? '') ?>#academicCalendarOffcanvas">
                                                Previous
                                            </a>
                                        <?php else: ?>
                                            <span class="page-link">Previous</span>
                                        <?php endif; ?>
                                    </li>

                                    <li class="page-item active">
                                        <span class="page-link"><?= $academicCalendarPage ?></span>
                                    </li>

                                    <li class="page-item <?= ($academicCalendarPage >= $totalAcdemicCalendarPages) ? 'disabled' : '' ?>">
                                        <?php if ($academicCalendarPage < $totalAcademicCalendarPages): ?>
                                            <a class="page-link" href="?academicCalendarPage=<?= $academicCalendarPage + 1 ?>&searchAcademicCalendar=<?= urlencode($_GET['searchAcademicCalendar'] ?? '') ?>&filterAcademicCalendar=<?= urlencode($_GET['filterAcademicCalendar'] ?? '') ?>#academicCalendarOffcanvas">
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
                                    <small>No academic calendar added yet</small>
                                    <small>Try to <a href="javascript:location.reload();"><i class="fa-solid fa-arrows-rotate"></i> reload</a> page</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Logs offcanvas -->
                <div class="offcanvas offcanvas-end" id="logsOffcanvas">
                    <?php
                        // Get locker logs
                        $limit = 16;
                        $logsPage = isset($_GET['logsPage']) ? (int)$_GET['logsPage'] : 1;
                        if ($logsPage < 1) $logsPage = 1;

                        $offset = ($logsPage - 1) * $limit;

                        $stmtLogs = $conn_local->prepare("CALL getLogs(?, ?)");
                        $stmtLogs->bind_param("ii", $limit, $offset);

                        $stmtLogs->execute();

                        $logsResultSet = $stmtLogs->get_result();

                        $stmtLogs->next_result();
                        $totalLogsRow = $stmtLogs->get_result()->fetch_assoc()['logsTotal'];

                        $totalPages = ceil($totalLogsRow / $limit);

                        $stmtLogs->close();

                        while ($conn_local->next_result()) {
                            $conn_local->store_result();
                        }
                    ?>

                    <div class="offcanvas-header">
                        <h3 class="offcanvas-title">Logs</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                    </div>

                    <div class="offcanvas-body">
                        <div class="table-container">
                            <?php if ($logsResultSet->num_rows > 0): ?>
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th>Action</th>
                                            <th>Timestamp</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php while ($logsRow = $logsResultSet->fetch_assoc()) { ?>
                                            <tr>
                                                <td data-label="Action">
                                                    <?php if ($logsRow['action'] == 'Activate') { ?>
                                                        <span class="badge rounded-pill primary-badge">
                                                            <?= $logsRow['action'] ?>
                                                        </span>

                                                    <?php } elseif ($logsRow['action'] == 'Deactivate') { ?>
                                                        <span class="badge rounded-pill danger-badge">
                                                            <?= $logsRow['action'] ?>
                                                        </span>

                                                    <?php } elseif ($logsRow['action'] == 'Add') { ?>
                                                        <span class="badge rounded-pill primary-badge">
                                                            <?= $logsRow['action'] ?>
                                                        </span>

                                                    <?php } elseif ($logsRow['action'] == 'Update') { ?>
                                                        <span class="badge rounded-pill warning-badge">
                                                            <?= $logsRow['action'] ?>
                                                        </span>

                                                    <?php } elseif ($logsRow['action'] == 'Delete') { ?>
                                                        <span class="badge rounded-pill danger-badge">
                                                            <?= $logsRow['action'] ?>
                                                        </span>

                                                    <?php } elseif ($logsRow['action'] == 'Apply') { ?>
                                                        <span class="badge rounded-pill success-badge">
                                                            <?= $logsRow['action'] ?>
                                                        </span>

                                                    <?php } elseif ($logsRow['action'] == 'Cancel') { ?>
                                                        <span class="badge rounded-pill pending-badge">
                                                            <?= $logsRow['action'] ?>
                                                        </span>

                                                    <?php } elseif ($logsRow['action'] == 'Accept') { ?>
                                                        <span class="badge rounded-pill accepted-badge">
                                                            <?= $logsRow['action'] ?>
                                                        </span>

                                                    <?php } elseif ($logsRow['action'] == 'Reject') { ?>
                                                        <span class="badge rounded-pill rejected-badge">
                                                            <?= $logsRow['action'] ?>
                                                        </span>

                                                    <?php } elseif ($logsRow['action'] == 'Revoke') { ?>
                                                        <span class="badge rounded-pill revoked-badge">
                                                            <?= $logsRow['action'] ?>
                                                        </span>

                                                    <?php } else { ?>
                                                        <span class="badge rounded-pill ended-badge">
                                                            <?= $logsRow['action'] ?>
                                                        </span>
                                                    <?php } ?>
                                                </td>

                                                <td data-label="Timestamp"><?= $logsRow['created_at'] ?></td>
                                                <td data-label="Description"><?= $logsRow['description'] ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div id="empty">
                                    <i class="fa-solid fa-ban"></i>
                                    <small>No logs yet</small>
                                    <small>Try to <a href="javascript:location.reload();"><i class="fa-solid fa-arrows-rotate"></i> reload</a> page</small>
                                </div>
                            <?php endif; ?>
                        </div>

                        <ul class="pagination">
                            <li class="page-item <?= ($logsPage <= 1) ? 'disabled' : '' ?>">
                                <?php if ($logsPage > 1): ?>
                                    <a class="page-link" href="?logsPage=<?= $logsPage - 1 ?>#logsOffcanvas">Previous</a>
                                <?php else: ?>
                                    <span class="page-link">Previous</span>
                                <?php endif; ?>
                            </li>

                            <li class="page-item active">
                                <span class="page-link"><?= $logsPage ?></span>
                            </li>

                            <li class="page-item <?= ($logsPage >= $totalPages) ? 'disabled' : '' ?>">
                                <?php if (mysqli_num_rows($logsResultSet) == $limit): ?>
                                    <a class="page-link" href="?logsPage=<?= $logsPage + 1 ?>#logsOffcanvas">Next</a>
                                <?php else: ?>
                                    <span class="page-link">Next</span>
                                <?php endif; ?>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Lockers -->
                <?php
                    $activeLockersTab = $_GET['tab'] ?? 'lockerSlotsTab';
                ?>

                <div class="locker-tabs">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link <?= ($_GET['tab'] ?? 'lockerSlotsTab') === 'lockerSlotsTab' ? 'active' : '' ?>" href="?tab=lockerSlotsTab">
                            Slots
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link <?= ($_GET['tab'] ?? '') === 'lockerLocationsTab' ? 'active' : '' ?>" href="?tab=lockerLocationsTab">
                                Locations
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link <?= ($_GET['tab'] ?? '') === 'lockerSizesTab' ? 'active' : '' ?>" href="?tab=lockerSizesTab">
                                Sizes
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Locker slots -->
                        <?php if ($activeLockersTab === 'lockerSlotsTab'): ?>
                            <div class="tab-pane fade <?= ($_GET['tab'] ?? 'lockerSlotsTab') === 'lockerSlotsTab' ? 'show active' : '' ?>" id="lockerSlotsTab">
                                <?php
                                    // Get locker locations
                                    $limit = 6;
                                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

                                    if ($page < 1) $page = 1;

                                    $offset = ($page - 1) * $limit;

                                    $search = trim($_GET['searchLockerSlotLocation'] ?? '');
                                    $filter = strtolower($_GET['filterLockerSlotLocation'] ?? '');

                                    $isSearching = !empty($search);
                                    $isFiltering = ($filter !== '');
                                    $isSearchFilterMode = $isSearching || $isFiltering;

                                    if ($isSearchFilterMode) {
                                        // Use search and filter
                                        $stmtLockerSlotLocation = $conn_local->prepare("CALL getSearchFilterLockerLocation(?, ?, ?, ?)");
                                        $stmtLockerSlotLocation->bind_param("ssii", $search, $filter, $limit, $offset);
                                    } else {
                                        // Use raw
                                        $stmtLockerSlotLocation = $conn_local->prepare("CALL getLockerLocations(?, ?)");
                                        $stmtLockerSlotLocation->bind_param("ii", $limit, $offset);
                                    }

                                    $stmtLockerSlotLocation->execute();
                                    $lockerSlotLocationResultSet = $stmtLockerSlotLocation->get_result();

                                    $stmtLockerSlotLocation->next_result();
                                    $totalLockerSlotLocationRow = $stmtLockerSlotLocation->get_result()->fetch_assoc()['lockerLocationsTotal'];

                                    $totalLockerSlotLocationPages = ceil($totalLockerSlotLocationRow / $limit);

                                    $stmtLockerSlotLocation->close();

                                    while ($conn_local->next_result()) {
                                        $conn_local->store_result();
                                    }
                                ?>

                                <header class="searchFilter">
                                    <form method="GET">
                                        <input type="hidden" name="tab" value="lockerSlotsTab">

                                        <div class="search-group">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                            <input type="search"
                                                name="searchLockerSlotLocation"
                                                placeholder="Search..."
                                                value="<?= htmlspecialchars($_GET['searchLockerSlotLocation'] ?? '') ?>">
                                        </div>

                                        <div class="filter-group">
                                            <i class="fa-solid fa-filter"></i>
                                            <select name="filterLockerSlotLocation" onchange="this.form.submit()">
                                                <option value="">All</option>
                                                <option value="a-z" <?= (($_GET['filterLockerSlotLocation'] ?? '') === 'a-z') ? 'selected' : '' ?>>A - Z</option>
                                                <option value="z-a" <?= (($_GET['filterLockerSlotLocation'] ?? '') === 'z-a') ? 'selected' : '' ?>>Z - A</option>
                                                <option value="newest" <?= (($_GET['filterLockerSlotLocation'] ?? '') === 'newest') ? 'selected' : '' ?>>Newest</option>
                                                <option value="oldest" <?= (($_GET['filterLockerSlotLocation'] ?? '') === 'oldest') ? 'selected' : '' ?>>Oldest</option>
                                            </select>
                                        </div>

                                        <button type="submit" hidden></button>
                                    </form>
                                </header>

                                <?php if ($lockerSlotLocationResultSet->num_rows > 0): ?>
                                    <?php while ($lockerSlotLocationRow = $lockerSlotLocationResultSet->fetch_assoc()) { ?>
                                        <div class="location-slot-section">
                                            <div class="location-header">
                                                <h3><?= $lockerSlotLocationRow['location'] ?></h3>

                                                <div class="action-buttons">
                                                    <button type="button"
                                                        class="sm-btn primary-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#addLockerModal<?= $lockerSlotLocationRow['id'] ?>">
                                                        <i class="fa-solid fa-plus"></i>
                                                        Add locker
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Add locker modal (per location) -->
                                            <div class="modal fade primary-modal" id="addLockerModal<?= $lockerSlotLocationRow['id'] ?>" tabindex="-1">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">

                                                        <div class="modal-header">
                                                            <h5 class="modal-title">
                                                                <i class="fa-solid fa-plus"></i>
                                                                Add Locker - <?= $lockerSlotLocationRow['location'] ?>
                                                            </h5>
                                                        </div>

                                                        <div class="modal-body">
                                                            <form method="POST" action="../app/locker/add_locker.php">
                                                                <input type="hidden" name="location_id" value="<?= $lockerSlotLocationRow['id'] ?>">

                                                                <div class="form-group">
                                                                    <label class="input-label">Slot Number</label>
                                                                    <div class="input-box">
                                                                        <input type="number" min="1" max="99" name="slot_number" required>
                                                                    </div>
                                                                </div>

                                                                <div class="form-group">
                                                                    <label class="input-label">Size</label>
                                                                    <div class="input-box">
                                                                        <select name="size_id" required>
                                                                            <?php
                                                                                $stmtSizes = $conn_local->prepare("CALL getLockerSizesNormal()");
                                                                                $stmtSizes->execute();
                                                                                $sizesResult = $stmtSizes->get_result();

                                                                                $stmtSizes->close();

                                                                                while ($conn_local->next_result()) {
                                                                                    $conn_local->store_result();
                                                                                }
                                                                            ?>
                                                                                <option value="">Select Size</option>
                                                                                <?php foreach ($sizesResult as $sizesList) { ?>
                                                                                    <option value="<?= $sizesList['id'] ?>">
                                                                                        <?= $sizesList['size'] ?>
                                                                                    </option>
                                                                                <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <div class="form-group">
                                                                    <label class="input-label">Academic Year</label>
                                                                    <div class="input-box">
                                                                        <select name="academic_year_id" required>
                                                                            <?php
                                                                                $stmtAcademicYear = $conn_local->prepare("CALL getAcademicCalendarNormal()");
                                                                                $stmtAcademicYear->execute();
                                                                                $academicYearResult = $stmtAcademicYear->get_result();

                                                                                $stmtAcademicYear->close();

                                                                                while ($conn_local->next_result()) {
                                                                                    $conn_local->store_result();
                                                                                }
                                                                            ?>

                                                                                <option value="">Select Academic Year</option>
                                                                                <?php foreach ($academicYearResult as $academicYearList) { ?>
                                                                                    <option value="<?= $academicYearList['id'] ?>">
                                                                                        <?= $academicYearList['academic_year'] ?> - <?= $academicYearList['semester'] ?>
                                                                                    </option>
                                                                                <?php } ?>
                                                                        </select>
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
                                                // Get locker slots by location
                                                $stmtLockerSlots = $conn_local->prepare("CALL getLockersByLocation(?)");
                                                $stmtLockerSlots->bind_param("i", $lockerSlotLocationRow['id']);
                                                $stmtLockerSlots->execute();

                                                $lockerSlotResultSet = $stmtLockerSlots->get_result();

                                                $stmtLockerSlots->free_result();
                                                $stmtLockerSlots->close();

                                                while ($conn_local->next_result()) {
                                                    $conn_local->store_result();
                                                }
                                            ?>

                                            <?php if ($lockerSlotResultSet->num_rows > 0): ?>
                                                <table class="table table-borderless">
                                                    <thead>
                                                        <tr>
                                                            <th>Slot Number</th>
                                                            <th>Size</th>
                                                            <th>Price</th>
                                                            <th>Academic Year - Semester</th>
                                                            <th>Start On</th>
                                                            <th>End On</th>
                                                            <th>Status</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        <?php while ($lockerSlotsRow = $lockerSlotResultSet->fetch_assoc()) { ?>
                                                            <tr>
                                                                <td data-label="Slot Number"><?= $lockerSlotsRow['slot_number'] ?></td>
                                                                <td data-label="Size"><?= $lockerSlotsRow['size'] ?></td>
                                                                <td data-label="Price">&#8369;<?= $lockerSlotsRow['price'] ?></td>

                                                                <td data-label="Academic Year - Semester">
                                                                    <?= $lockerSlotsRow['academic_year'] ?> - <?= $lockerSlotsRow['semester'] ?>
                                                                </td>

                                                                <td data-label="Start On">
                                                                    <?= $lockerSlotsRow['start_at'] ?>
                                                                </td>

                                                                <td data-label="End On">
                                                                    <?= $lockerSlotsRow['end_at'] ?>
                                                                </td>

                                                                <td data-label="Status">
                                                                    <?= $lockerSlotsRow['status'] ?>

                                                                    <?php if (!empty($lockerSlotsRow['user_id'])) { ?>
                                                                        <br>By <?= $lockerSlotsRow['user_id'] ?>
                                                                    <?php } ?>
                                                                </td>

                                                                <td data-label="Action">
                                                                    <div class="action-buttons">
                                                                        <button type="button"
                                                                            class="sm-btn primary-btn"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#updateLockerSlotModal<?= $lockerSlotsRow['id'] ?>">
                                                                            <i class="fa-solid fa-pen"></i>
                                                                            Edit
                                                                        </button>

                                                                        <button type="button"
                                                                            class="sm-btn danger-btn"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#deleteLockerSlotConfirmationModal<?= $lockerSlotsRow['id'] ?>">
                                                                            <i class="fa-solid fa-trash"></i>
                                                                            Delete
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                            </tr>

                                                            <!-- Update locker modal -->
                                                            <div class="modal fade primary-modal" id="updateLockerSlotModal<?= $lockerSlotsRow['id'] ?>" tabindex="-1">
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
                                                                                <input type="hidden" name="id" value="<?= $lockerSlotsRow['id'] ?>">
                                                                                
                                                                                <div class="form-group">
                                                                                    <label class="input-label">Slot Number</label>
                                                                                    <div class="input-box">
                                                                                        <input type="number" min="1" max="99" name="slot_number" value="<?= $lockerSlotsRow['slot_number'] ?>" required>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="form-group">
                                                                                    <label class="input-label">Size</label>
                                                                                    <div class="input-box">
                                                                                        <select name="size_id" required>
                                                                                            <?php
                                                                                                $sizesResult = $conn_local->query("SELECT * FROM locker_sizes");
                                                                                            ?>
                                                                                                <?php while ($sizesList = $sizesResult->fetch_assoc()) { ?>
                                                                                                    <option value="<?= $sizesList['id'] ?>"
                                                                                                        <?= ($lockerSlotsRow['size_id'] == $sizesList['id']) ? 'selected' : '' ?>>
                                                                                                        <?= $sizesList['size'] ?>
                                                                                                    </option>
                                                                                                <?php } ?>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="form-group">
                                                                                    <label class="input-label">Academic Year</label>
                                                                                    <div class="input-box">
                                                                                        <select name="academic_year_id" required>
                                                                                            <?php
                                                                                                $academicYearResult = $conn_local->query("SELECT * FROM academic_calendar");
                                                                                            ?>
                                                                                                <?php while ($academicYearList = $academicYearResult->fetch_assoc()) { ?>
                                                                                                    <option value="<?= $academicYearList['id'] ?>"
                                                                                                        <?= ($lockerSlotsRow['academic_year_id'] == $academicYearList['id']) ? 'selected' : '' ?>>
                                                                                                        <?= $academicYearList['academic_year'] ?> - <?= $academicYearList['semester'] ?>
                                                                                                    </option>
                                                                                                <?php } ?>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="form-group">
                                                                                    <label class="input-label">Status</label>
                                                                                    <div class="input-box">
                                                                                        <select name="status">
                                                                                            <option value="Not yet started"
                                                                                                <?= $lockerSlotsRow['status'] == 'Not yet started' ? 'selected' : '' ?>>
                                                                                                Not yet started
                                                                                            </option>

                                                                                            <option value="Already closed"
                                                                                                <?= $lockerSlotsRow['status'] == 'Already closed' ? 'selected' : '' ?>>
                                                                                                Already closed
                                                                                            </option>

                                                                                            <option value="Available"
                                                                                                <?= $lockerSlotsRow['status'] == 'Available' ? 'selected' : '' ?>>
                                                                                                Available
                                                                                            </option>

                                                                                            <option value="Occupied"
                                                                                                <?= $lockerSlotsRow['status'] == 'Occupied' ? 'selected' : '' ?>>
                                                                                                Occupied
                                                                                            </option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="action-buttons">
                                                                                    <button type="submit" class="btn primary-btn">Save Changes</button>
                                                                                    <button type="button" class="btn secondary-btn" data-bs-dismiss="modal">
                                                                                        Cancel
                                                                                    </button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Delete locker confirmation modal -->
                                                            <div class="modal fade danger-modal" id="deleteLockerSlotConfirmationModal<?= $lockerSlotsRow['id'] ?>" tabindex="-1" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered">
                                                                    <div class="modal-content">
                                                                        <div class="modal-body">
                                                                            <div class="message">
                                                                                <p><i class="fa-solid fa-circle-exclamation"></i></p>
                                                                                <h5>Delete</h5>
                                                                                <p>Are you sure you want to delete <span>slot <?= $lockerSlotsRow['slot_number'] ?></span>?</p>
                                                                            </div>

                                                                            <div class="action-buttons">
                                                                                <button type="button" class="btn secondary-btn" data-bs-dismiss="modal">
                                                                                    Cancel
                                                                                </button>

                                                                                <form method="POST" action="../app/locker/delete_locker.php">
                                                                                    <input type="hidden" name="id" value="<?= $lockerSlotsRow['id'] ?>">

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
                                                    <small>Try to <a href="javascript:location.reload();"><i class="fa-solid fa-arrows-rotate"></i> reload</a> page or <a data-bs-toggle="modal" data-bs-target="#addLockerModal<?= $lockerSlotLocationRow['id'] ?>"><i class="fa-solid fa-plus"></i> add slot</a></small>
                                                </div>
                                            <?php endif; ?>
                                        </div>                        
                                    <?php } ?>

                                    <ul class="pagination">
                                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                            <?php if ($page > 1): ?>
                                                <a class="page-link" href="?page=<?= $page - 1 ?>&searchLockerSlotLocation=<?= urlencode($_GET['searchLockerSlotLocation'] ?? '') ?>&filterLockerSlotLocation=<?= urlencode($_GET['filterLockerSlotLocation'] ?? '') ?>">
                                                    Previous
                                                </a>
                                            <?php else: ?>
                                                <span class="page-link">Previous</span>
                                            <?php endif; ?>
                                        </li>

                                        <li class="page-item active">
                                            <span class="page-link"><?= $page ?></span>
                                        </li>

                                        <li class="page-item <?= ($page >= $totalLockerSlotLocationPages) ? 'disabled' : '' ?>">
                                            <?php if ($page < $totalLockerSlotLocationPages): ?>
                                                <a class="page-link" href="?page=<?= $page + 1 ?>&searchLockerSlotLocation=<?= urlencode($_GET['searchLockerSlotLocation'] ?? '') ?>&filterLockerSlotLocation=<?= urlencode($_GET['filterLockerSlotLocation'] ?? '') ?>">
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
                                        <small>No locker location yet</small>
                                        <small>Try to <a href="javascript:location.reload();"><i class="fa-solid fa-arrows-rotate"></i> reload</a> page or <a data-bs-toggle="modal" data-bs-target="#addLockerLocationModal"><i class="fa-solid fa-plus"></i> add location</a></small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Locker locations -->
                        <?php if ($activeLockersTab === 'lockerLocationsTab'): ?>
                            <div class="tab-pane fade <?= ($_GET['tab'] ?? '') === 'lockerLocationsTab' ? 'show active' : '' ?>" id="lockerLocationsTab">
                                <?php
                                    // Get locker locations
                                    $limit = 6;
                                    $lockerLocationsPage = isset($_GET['lockerLocationsPage']) ? (int)$_GET['lockerLocationsPage'] : 1;

                                    if ($lockerLocationsPage < 1) $lockerLocationsPage = 1;

                                    $offset = ($lockerLocationsPage - 1) * $limit;

                                    $search = trim($_GET['searchLockerLocations'] ?? '');
                                    $filter = strtolower($_GET['filterLockerLocations'] ?? '');

                                    $isSearching = !empty($search);
                                    $isFiltering = ($filter !== '');
                                    $isSearchFilterMode = $isSearching || $isFiltering;

                                    if ($isSearchFilterMode) {
                                        // Use search and filter
                                        $stmtLockerLocation = $conn_local->prepare("CALL getSearchFilterLockerLocation(?, ?, ?, ?)");
                                        $stmtLockerLocation->bind_param("ssii", $search, $filter, $limit, $offset);
                                    } else {
                                        // Use raw
                                        $stmtLockerLocation = $conn_local->prepare("CALL getLockerLocations(?, ?)");
                                        $stmtLockerLocation->bind_param("ii", $limit, $offset);
                                    }

                                    $stmtLockerLocation->execute();
                                    $lockerLocationResultSet = $stmtLockerLocation->get_result();

                                    $stmtLockerLocation->next_result();
                                    $totalLockerLocationRow = $stmtLockerLocation->get_result()->fetch_assoc()['lockerLocationsTotal'];

                                    $totalLockerLocationPages = ceil($totalLockerLocationRow / $limit);

                                    $stmtLockerLocation->close();

                                    while ($conn_local->next_result()) {
                                        $conn_local->store_result();
                                    }
                                ?>

                                <header class="searchFilter">
                                    <form method="GET">
                                        <input type="hidden" name="tab" value="lockerLocationsTab">

                                        <div class="search-group">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                            <input type="search"
                                                name="searchLockerLocations"
                                                placeholder="Search..."
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

                                        <button type="button" class="btn primary-btn" data-bs-toggle="modal" data-bs-target="#addLockerLocationModal">
                                            <i class="fa-solid fa-plus"></i>
                                        </button>
                                    </form>
                                </header>

                                <div class="table-container">
                                    <?php if ($lockerLocationResultSet->num_rows > 0): ?>
                                        <table class="table table-borderless">
                                            <thead>
                                                <tr>
                                                    <th>Location</th>
                                                    <th>Created At</th>
                                                    <th>Updated At</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php while ($lockerLocationRow = $lockerLocationResultSet->fetch_assoc()) { ?>
                                                    <tr>
                                                        <td data-label="Location"><?= $lockerLocationRow['location'] ?></td>
                                                        <td data-label="Created At"><?= $lockerLocationRow['created_at'] ?></td>
                                                        <td data-label="Updated At"><?= $lockerLocationRow['updated_at'] ?></td>

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

                                        <ul class="pagination">
                                            <li class="page-item <?= ($lockerLocationsPage <= 1) ? 'disabled' : '' ?>">
                                                <?php if ($lockerLocationsPage > 1): ?>
                                                    <a class="page-link" href="?lockerLocationsPage=<?= $lockerLocationsPage - 1 ?>&searchLockerLocations=<?= urlencode($_GET['searchLockerLocations'] ?? '') ?>&filterLockerLocations=<?= urlencode($_GET['filterLockerLocations'] ?? '') ?>#lockerLocationsOffcanvas">
                                                        Previous
                                                    </a>
                                                <?php else: ?>
                                                    <span class="page-link">Previous</span>
                                                <?php endif; ?>
                                            </li>

                                            <li class="page-item active">
                                                <span class="page-link"><?= $lockerLocationsPage ?></span>
                                            </li>

                                            <li class="page-item <?= ($lockerLocationsPage >= $totalLockerLocationPages) ? 'disabled' : '' ?>">
                                                <?php if ($lockerLocationsPage < $totalLockerLocationPages): ?>
                                                    <a class="page-link" href="?lockerLocationsPage=<?= $lockerLocationsPage + 1 ?>&searchLockerLocations=<?= urlencode($_GET['searchLockerLocations'] ?? '') ?>&filterLockerLocations=<?= urlencode($_GET['filterLockerLocations'] ?? '') ?>#lockerLocationsOffcanvas">
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
                                            <small>No locker location yet</small>
                                            <small>Try to <a href="javascript:location.reload();"><i class="fa-solid fa-arrows-rotate"></i> reload</a> page or <a data-bs-toggle="modal" data-bs-target="#addLockerLocationModal"><i class="fa-solid fa-plus"></i> add location</a></small>
                                        </div>
                                    <?php endif; ?>
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
                                    $stmtLockerLocation = $conn_local->prepare("CALL getLockerLocations(?, ?)");
                                    $stmtLockerLocation->bind_param("ii", $limit, $offset);

                                    $stmtLockerLocation->execute();
                                    $lockerLocationResultSet = $stmtLockerLocation->get_result();

                                    $stmtLockerLocation->close();

                                    while ($conn_local->next_result()) {
                                        $conn_local->store_result();
                                    }

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
                            </div>
                        <?php endif; ?>

                        <!-- Locker sizes -->
                        <?php if ($activeLockersTab === 'lockerSizesTab'): ?>
                            <div class="tab-pane fade <?= ($_GET['tab'] ?? '') === 'lockerSizesTab' ? 'show active' : '' ?>" id="lockerSizesTab">
                                <?php
                                    // Get locker sizes
                                    $limit = 6;
                                    $lockerSizesPage = isset($_GET['lockerSizesPage']) ? (int)$_GET['lockerSizesPage'] : 1;

                                    if ($lockerSizesPage < 1) $lockerSizesPage = 1;

                                    $offset = ($lockerSizesPage - 1) * $limit;

                                    $search = trim($_GET['searchLockerSizes'] ?? '');
                                    $filter = strtolower($_GET['filterLockerSizes'] ?? '');

                                    $isSearching = !empty($search);
                                    $isFiltering = ($filter !== '');
                                    $isSearchFilterMode = $isSearching || $isFiltering;

                                    if ($isSearchFilterMode) {
                                        // Use search and filter
                                        $stmtLockerSizes = $conn_local->prepare("CALL getSearchFilterLockerSizes(?, ?, ?, ?)");
                                        $stmtLockerSizes->bind_param("ssii", $search, $filter, $limit, $offset);
                                    } else {
                                        // Use raw
                                        $stmtLockerSizes = $conn_local->prepare("CALL getLockerSizes(?, ?)");
                                        $stmtLockerSizes->bind_param("ii", $limit, $offset);
                                    }

                                    $stmtLockerSizes->execute();
                                    $lockerSizesResultSet = $stmtLockerSizes->get_result();

                                    $stmtLockerSizes->next_result();
                                    $totalLockerSizesRow = $stmtLockerSizes->get_result()->fetch_assoc()['lockerSizesTotal'];

                                    $totalLockerSizesPages = ceil($totalLockerSizesRow / $limit);

                                    $stmtLockerSizes->close();

                                    while ($conn_local->next_result()) {
                                        $conn_local->store_result();
                                    }
                                ?>

                                <header class="searchFilter">
                                    <form method="GET">
                                        <input type="hidden" name="tab" value="lockerSizesTab">

                                        <div class="search-group">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                            <input type="search"
                                                name="searchLockerSizes"
                                                placeholder="Search..."
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

                                        <button type="button" class="btn primary-btn" data-bs-toggle="modal" data-bs-target="#addLockerSizeModal">
                                            <i class="fa-solid fa-plus"></i>
                                        </button>
                                    </form>
                                </header>

                                <div class="table-container">
                                    <?php if ($lockerSizesResultSet->num_rows > 0): ?>
                                        <table class="table table-borderless">
                                            <thead>
                                                <tr>
                                                    <th>Size</th>
                                                    <th>Price</th>
                                                    <th>Created At</th>
                                                    <th>Updated At</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php while ($lockerSizesRow = $lockerSizesResultSet->fetch_assoc()) { ?>
                                                    <tr>
                                                        <td data-label="Size"><?= $lockerSizesRow['size'] ?></td>
                                                        <td data-label="Price">&#8369;<?= $lockerSizesRow['price'] ?></td>
                                                        <td data-label="Created At"><?= $lockerSizesRow['created_at'] ?></td>
                                                        <td data-label="Updated At"><?= $lockerSizesRow['updated_at'] ?></td>

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

                                        <ul class="pagination">
                                            <li class="page-item <?= ($lockerSizesPage <= 1) ? 'disabled' : '' ?>">
                                                <?php if ($lockerSizesPage > 1): ?>
                                                    <a class="page-link" href="?lockerSizesPage=<?= $lockerSizesPage - 1 ?>&searchLockerSizes=<?= urlencode($_GET['searchLockerSizes'] ?? '') ?>&filterLockerSizes=<?= urlencode($_GET['filterLockerSizes'] ?? '') ?>#lockerSizesOffcanvas">
                                                        Previous
                                                    </a>
                                                <?php else: ?>
                                                    <span class="page-link">Previous</span>
                                                <?php endif; ?>
                                            </li>

                                            <li class="page-item active">
                                                <span class="page-link"><?= $lockerSizesPage ?></span>
                                            </li>

                                            <li class="page-item <?= ($lockerSizesPage >= $totalLockerSizesPages) ? 'disabled' : '' ?>">
                                                <?php if ($lockerSizesPage < $totalLockerSizesPages): ?>
                                                    <a class="page-link" href="?lockerSizesPage=<?= $lockerSizesPage + 1 ?>&searchLockerSizes=<?= urlencode($_GET['searchLockerSizes'] ?? '') ?>&filterLockerSizes=<?= urlencode($_GET['filterLockerSizes'] ?? '') ?>#lockerSizesOffcanvas">
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
                                            <small>No locker size yet</small>
                                            <small>Try to <a href="javascript:location.reload();"><i class="fa-solid fa-arrows-rotate"></i> reload</a> page or <a data-bs-toggle="modal" data-bs-target="#addLockerSizeModal"><i class="fa-solid fa-plus"></i> add size</a></small>
                                        </div>
                                    <?php endif; ?>
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
                                    $stmtLockerSizes = $conn_local->prepare("CALL getLockerSizes(?, ?)");
                                    $stmtLockerSizes->bind_param("ii", $limit, $offset);

                                    $stmtLockerSizes->execute();
                                    $lockerSizesResultSet = $stmtLockerSizes->get_result();

                                    $stmtLockerSizes->close();

                                    while ($conn_local->next_result()) {
                                        $conn_local->store_result();
                                    }

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
