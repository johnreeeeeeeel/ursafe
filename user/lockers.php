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
                <a href="home.php">
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
            <a href="home.php">
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

                        <div class="table-container">
                            <?php
                                // Get my locker application
                                $search = trim($_GET['searchMyLockerApplication'] ?? '');
                                $filter = strtolower($_GET['filterMyLockerApplication'] ?? '');

                                if (!empty($search) || !empty($filter)) {
                                    // Use search and filter
                                    $stmtMyLockerApplication = $conn_local->prepare("CALL getSearchFilterMyLockerApplication(?, ?, ?)");
                                    $stmtMyLockerApplication->bind_param("sss", $id, $search, $filter);
                                } else {
                                    // Use raw
                                    $stmtMyLockerApplication = $conn_local->prepare("CALL getMyLockerApplications(?)");
                                        $stmtMyLockerApplication->bind_param("s", $id);
                                }

                                $stmtMyLockerApplication->execute();
                                $myLockerApplicationResultSet = $stmtMyLockerApplication->get_result();
                                $stmtMyLockerApplication->close();

                                while ($conn_local->next_result()) {
                                    $conn_local->store_result();
                                }
                            ?>

                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th>Location</th>
                                        <th>Slot</th>
                                        <th>Size</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php while ($myLockerApplicationRow = $myLockerApplicationResultSet->fetch_assoc()) { ?>
                                        <tr>
                                            <td data-label="Location"><?= $myLockerApplicationRow['location'] ?></td>
                                            <td data-label="Slot"><?= $myLockerApplicationRow['slot_number'] ?></td>
                                            <td data-label="Size"><?= $myLockerApplicationRow['size'] ?></td>
                                            <td data-label="Price"><?= $myLockerApplicationRow['price'] ?></td>

                                            <td data-label="Status">
                                                <?php if ($myLockerApplicationRow['status'] == 'Pending') { ?>
                                                    <span class="badge rounded-pill pending-badge">Pending</span>

                                                <?php } elseif ($myLockerApplicationRow['status'] == 'Accepted') { ?>
                                                    <span class="badge rounded-pill accepted-badge">Accepted</span>

                                                <?php } elseif ($myLockerApplicationRow['status'] == 'Revoked') { ?>
                                                    <span class="badge rounded-pill revoked-badge">Revoked</span>

                                                <?php } elseif ($myLockerApplicationRow['status'] == 'Cancelled') { ?>
                                                    <span class="badge rounded-pill cancelled-badge">Cancelled</span>
                                                
                                                <?php } else { ?>
                                                    <span class="badge rounded-pill rejected-badge">Rejected</span>
                                                <?php }?>
                                            </td>

                                            <td data-label="Action">
                                                <?php if ($myLockerApplicationRow['status'] == 'Pending') { ?>
                                                    <div class="action-buttons">
                                                        <button class="sm-btn danger-btn"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#cancelApplicationModal<?= $myLockerApplicationRow['application_id'] ?>">
                                                            <i class="fa-solid fa-circle-xmark"></i>
                                                            Cancel
                                                        </button>
                                                    </div>
                                                <?php } else { ?>

                                                    <small>No Action</small>

                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <?php
                    $stmtModalLockerApplication = $conn_local->prepare("CALL getMyLockerApplications(?)");
                    $stmtModalLockerApplication->bind_param("s", $id);
                    $stmtModalLockerApplication->execute();

                    $mylockerApplicationResultSet = $stmtModalLockerApplication->get_result();
                ?>

                <?php while ($myLockerApplicationRow = $mylockerApplicationResultSet->fetch_assoc()) { ?>
                    
                    <div class="modal fade danger-modal"
                        id="cancelApplicationModal<?= $myLockerApplicationRow['application_id'] ?>"
                        tabindex="-1">

                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">

                                <div class="modal-body">
                                    <div class="message">
                                        <i class="fa-solid fa-circle-xmark"></i>
                                        <h5>Cancel Application</h5>
                                        <p>
                                            Are you sure you want to cancel your application for <span>Slot <?= $myLockerApplicationRow['slot_number'] ?></span>?
                                        </p>
                                    </div>

                                    <div class="action-buttons">
                                        <button type="button" class="btn secondary-btn" data-bs-dismiss="modal">
                                            No
                                        </button>

                                        <form method="POST" action="../app/locker/cancel_locker_application.php">
                                            <input type="hidden" name="id" value="<?= $myLockerApplicationRow['application_id'] ?>">
                                            <button type="submit" class="btn primary-btn">
                                                Yes, Cancel
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php } ?>

                <?php
                $stmtModalLockerApplication->close();

                while ($conn_local->next_result()) {
                    $conn_local->store_result();
                }
                ?>

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