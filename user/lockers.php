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
                                View My Locker Applications
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

                <!-- Offcanvas for my locker application -->
                <div class="offcanvas offcanvas-end" id="myLockerApplicationOffcanvas">
                    <div class="offcanvas-header">
                        <h3 class="offcanvas-title">My Locker Applications</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                    </div>

                    <div class="offcanvas-body">
                        <header>
                            <form method="GET">
                                <div class="search-group">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    <input type="search"
                                        name="searchMyLockerApplication"
                                        placeholder="Search my locker applications..."
                                        value="<?= htmlspecialchars($_GET['searchMyLockerApplication'] ?? '') ?>">
                                </div>

                                <div class="filter-group">
                                    <i class="fa-solid fa-filter"></i>
                                    <select name="filterMyLockerApplication" onchange="this.form.submit()">
                                        <option value="">All</option>
                                        <option value="asc" <?= (($_GET['filterMyLockerApplication'] ?? '') === 'asc') ? 'selected' : '' ?>>A - Z</option>
                                        <option value="desc" <?= (($_GET['filterMyLockerApplication'] ?? '') === 'desc') ? 'selected' : '' ?>>Z - A</option>
                                    </select>
                                </div>

                                <button type="submit" hidden></button>
                            </form>
                        </header>

                        <div class="locker-applications-container">
                            <?php
                                // Accepted locker application
                                $stmtAccepted = $conn_local->prepare("CALL getMyAcceptedLockerApplications(?)");
                                $stmtAccepted->bind_param("s", $id);
                                $stmtAccepted->execute();
                                $acceptedResultSet = $stmtAccepted->get_result();
                                $stmtAccepted->close();

                                while ($conn_local->next_result()) { $conn_local->store_result(); }

                                // Pending locker application
                                $stmtPending = $conn_local->prepare("CALL getMyPendingLockerApplications(?)");
                                $stmtPending->bind_param("s", $id);
                                $stmtPending->execute();
                                $pendingResultSet = $stmtPending->get_result();
                                $stmtPending->close();

                                while ($conn_local->next_result()) { $conn_local->store_result(); }

                                // Locker application history 
                                $stmtHistory = $conn_local->prepare("CALL getMyLockerApplicationHistory(?)");
                                $stmtHistory->bind_param("s", $id);
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
                                            <p><span>Location</span> <span><?= $row['location'] ?></span></p>
                                            <p><span>Slot</span> <span><?= $row['slot_number'] ?></span></p>
                                            <p><span>Size</span> <span><?= $row['size'] ?></span></p>
                                            <p><span>Price</span> <span><?= $row['price'] ?></span></p>
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
                                            <p><span>Location</span> <span><?= $row['location'] ?></span></p>
                                            <p><span>Slot</span> <span><?= $row['slot_number'] ?></span></p>
                                            <p><span>Size</span> <span><?= $row['size'] ?></span></p>
                                            <p><span>Price</span> <span><?= $row['price'] ?></span></p>

                                            <div class="action-buttons">
                                                <button class="sm-btn danger-btn" data-bs-toggle="modal" data-bs-target="#cancelApplicationModal<?= $row['application_id'] ?>">
                                                    <i class="fa-solid fa-xmark"></i>
                                                    Cancel Application
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
                                    <?php while($lockerRow = $lockerResultSet->fetch_assoc()) { ?>
                                        <tr>
                                        <td data-label="Slot Number"><?= $lockerRow['slot_number'] ?></td>
                                        <td data-label="Size"><?= $lockerRow['size'] ?></td>
                                        <td data-label="Price"><?= $lockerRow['price'] ?></td>
                                        <td data-label="Status"><?= $lockerRow['status'] ?></td>

                                        <td data-label="Action">
                                            <div class="action-buttons">
                                                <button 
                                                    type="button" 
                                                    class="sm-btn secondary-btn"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#applyLockerSlotModal<?= $lockerRow['id'] ?>">

                                                    <i class="fa-solid fa-file"></i>
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
                                                            <i class="fa-solid fa-circle-check"></i>
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