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
                            <button type="button" class="dropdown-item" data-bs-toggle="offcanvas" data-bs-target="#lockerApplicationOffcanvas" onclick="window.location.hash='lockerApplicationOffcanvas';">
                                View Locker Applications
                            </button>
                        </li>
                        <li><hr class="dropdown-divider"></hr></li>
                        <li>
                            <button type="button" class="dropdown-item" data-bs-toggle="offcanvas" data-bs-target="#lockerLocationsOffcanvas" onclick="window.location.hash='lockerLocationsOffcanvas';">
                                View Locker Locations
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item" data-bs-toggle="offcanvas" data-bs-target="#lockerSizesOffcanvas" onclick="window.location.hash='lockerSizesOffcanvas';">
                                View Locker Sizes
                            </button> 
                        </li>
                        <li><hr class="dropdown-divider"></hr></li>
                        <li>
                            <button type="button" class="dropdown-item" data-bs-toggle="offcanvas" data-bs-target="#lockerLogsOffcanvas" onclick="window.location.hash='lockerLogsOffcanvas';">
                                View Locker Logs
                            </button> 
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <div class="content">
            <div id="lockers">
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
                                <option value="asc" <?= (($_GET['filterLocation'] ?? '') === 'asc') ? 'selected' : '' ?>>A - Z</option>
                                <option value="desc" <?= (($_GET['filterLocation'] ?? '') === 'desc') ? 'selected' : '' ?>>Z - A</option>
                            </select>
                        </div>

                        <button type="submit" hidden></button>
                    </form>
                </header>

                <!-- Offcanvas for locker application -->
                <div class="offcanvas offcanvas-end" id="lockerApplicationOffcanvas">
                    <div class="offcanvas-header">
                        <h3 class="offcanvas-title">Locker Applications</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                    </div>

                    <div class="offcanvas-body">
                        <header>
                            <form method="GET">
                                <div class="search-group">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    <input type="search"
                                        name="searchLockerApplication"
                                        placeholder="Search locker applications..."
                                        value="<?= htmlspecialchars($_GET['searchLockerApplication'] ?? '') ?>">
                                </div>

                                <div class="filter-group">
                                    <i class="fa-solid fa-filter"></i>
                                    <select name="filterLockerApplication" onchange="this.form.submit()">
                                        <option value="">All</option>
                                        <option value="asc" <?= (($_GET['filterLockerApplication'] ?? '') === 'asc') ? 'selected' : '' ?>>A - Z</option>
                                        <option value="desc" <?= (($_GET['filterLockerApplication'] ?? '') === 'desc') ? 'selected' : '' ?>>Z - A</option>
                                    </select>
                                </div>

                                <button type="submit" hidden></button>
                            </form>
                        </header>

                        <div class="locker-applications-container">
                            <?php
                                // Accepted locker application
                                $stmtAccepted = $conn_local->prepare("CALL getAcceptedLockerApplications()");
                                $stmtAccepted->execute();
                                $acceptedResultSet = $stmtAccepted->get_result();
                                $stmtAccepted->close();

                                while ($conn_local->next_result()) { $conn_local->store_result(); }

                                // Pending locker application
                                $stmtPending = $conn_local->prepare("CALL getPendingLockerApplications()");
                                $stmtPending->execute();
                                $pendingResultSet = $stmtPending->get_result();
                                $stmtPending->close();

                                while ($conn_local->next_result()) { $conn_local->store_result(); }

                                // Locker application history 
                                $stmtHistory = $conn_local->prepare("CALL getLockerApplicationHistory()");
                                $stmtHistory->execute();
                                $historyResultSet = $stmtHistory->get_result();
                                $stmtHistory->close();

                                while ($conn_local->next_result()) { $conn_local->store_result(); }
                            ?>

                            <!-- Accepted applications -->
                            <div class="card-container">
                                <h3>Accepted Applications</h3>

                                <div class="cards">
                                    <?php while ($row = $acceptedResultSet->fetch_assoc()) { ?>
                                        <div class="card accepted">
                                            <p><span>Application ID</span> <span><?= $row['application_id'] ?></span></p>
                                            <p><span>User ID</span> <span><?= $row['user_id'] ?></span></p>
                                            <p><span>Fullname</span> <span><?= $row['fullname'] ?></span></p>
                                            <p><span>Location</span> <span><?= $row['location'] ?></span></p>
                                            <p><span>Slot</span> <span><?= $row['slot_number'] ?></span></p>
                                            <p><span>Size</span> <span><?= $row['size'] ?></span></p>
                                            <p><span>Price</span> <span><?= $row['price'] ?></span></p>

                                            <div class="action-buttons">
                                                <button class="sm-btn danger-btn" data-bs-toggle="modal" data-bs-target="#revokeLockerApplicationModal<?= $row['application_id'] ?>">
                                                    <i class="fa-solid fa-ban"></i>
                                                    Revoke
                                                </button>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <!-- Pending applications -->
                            <div class="card-container">
                                <h3>Pending Applications</h3>

                                <div class="cards">
                                    <?php while ($row = $pendingResultSet->fetch_assoc()) { ?>
                                        <div class="card pending">
                                            <p><span>Application ID</span> <span><?= $row['application_id'] ?></span></p>
                                            <p><span>User ID</span> <span><?= $row['user_id'] ?></span></p>
                                            <p><span>Fullname</span> <span><?= $row['fullname'] ?></span></p>
                                            <p><span>Location</span> <span><?= $row['location'] ?></span></p>
                                            <p><span>Slot</span> <span><?= $row['slot_number'] ?></span></p>
                                            <p><span>Size</span> <span><?= $row['size'] ?></span></p>
                                            <p><span>Price</span> <span><?= $row['price'] ?></span></p>

                                            <div class="action-buttons">
                                                <button class="sm-btn primary-btn" data-bs-toggle="modal" data-bs-target="#acceptLockerApplicationModal<?= $row['application_id'] ?>">
                                                    <i class="fa-solid fa-check"></i>
                                                    Accept
                                                </button>

                                                <button class="sm-btn danger-btn" data-bs-toggle="modal" data-bs-target="#rejectLockerApplicationModal<?= $row['application_id'] ?>">
                                                    <i class="fa-solid fa-xmark"></i>
                                                    Reject
                                                </button>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <!-- Application history -->
                            <table class="table table-borderless">
                                <h3>Application History</h3>

                                <thead>
                                    <tr>
                                        <th>Application ID</th>
                                        <th>User ID</th>
                                        <th>Fullname</th>
                                        <th>Location</th>
                                        <th>Slot</th>
                                        <th>Size</th>
                                        <th>Price</th>
                                        <th>Status</th>
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
                                            <td data-label="Price"><?= $row['price'] ?></td>

                                            <td data-label="Status">
                                                <?php if ($row['status'] == 'Revoked') { ?>
                                                    <span class="badge rounded-pill revoked-badge">Revoked</span>

                                                <?php } elseif ($row['status'] == 'Cancelled') { ?>
                                                    <span class="badge rounded-pill cancelled-badge">Cancelled</span>

                                                <?php } else { ?>
                                                    <span class="badge rounded-pill rejected-badge">Rejected</span>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
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
                
                <!-- Offcanvas for locker locations -->
                <div class="offcanvas offcanvas-end" id="lockerLocationsOffcanvas">
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
                                        <option value="asc" <?= (($_GET['filterLockerLocations'] ?? '') === 'asc') ? 'selected' : '' ?>>A - Z</option>
                                        <option value="desc" <?= (($_GET['filterLockerLocations'] ?? '') === 'desc') ? 'selected' : '' ?>>Z - A</option>
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

                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th>Location</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php while ($lockerLocationRow = $lockerLocationResultSet->fetch_assoc()) { ?>
                                        <tr>
                                            <td data-label="Location"><?= $lockerLocationRow['location'] ?></td>

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
                        </div>
                    </div>
                </div>

                <?php
                    $lockerLocationResultSet = $conn_local->query("CALL getLockerLocations()");
                    $conn_local->next_result();

                    while ($lockerLocationRow = $lockerLocationResultSet->fetch_assoc()) {
                ?>

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
                                        <option value="asc" <?= (($_GET['filterLockerSizes'] ?? '') === 'asc') ? 'selected' : '' ?>>A - Z</option>
                                        <option value="desc" <?= (($_GET['filterLockerSizes'] ?? '') === 'desc') ? 'selected' : '' ?>>Z - A</option>
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

                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th>Size</th>
                                        <th>Price</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php while ($lockerSizesRow = $lockerSizesResultSet->fetch_assoc()) { ?>
                                        <tr>
                                            <td data-label="Size"><?= $lockerSizesRow['size'] ?></td>
                                            <td data-label="Price"><?= $lockerSizesRow['price'] ?></td>

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
                        </div>
                    </div>
                </div>

                <?php
                    $lockerSizesResultSet = $conn_local->query("CALL getLockerSizes()");
                    $conn_local->next_result();

                    while ($lockerSizesRow = $lockerSizesResultSet->fetch_assoc()) {
                ?>
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
                    <div class="offcanvas-header">
                        <h3 class="offcanvas-title">Locker Logs</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                    </div>

                    <div class="offcanvas-body">
                        <div class="table-container">
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

                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th>Slot Number</th>
                                        <th>Size</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php while ($lockerRow = $lockerResultSet->fetch_assoc()) { ?>
                                        <tr>
                                            <td data-label="Slot Number"><?= $lockerRow['slot_number'] ?></td>
                                            <td data-label="Size"><?= $lockerRow['size'] ?></td>
                                            <td data-label="Price"><?= $lockerRow['price'] ?></td>
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
                        </div>                        
                    <?php } ?>
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
