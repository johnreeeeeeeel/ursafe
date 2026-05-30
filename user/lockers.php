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
                    
                    <li>
                        <a class="link" href="my_applications.php">
                            <i class="fa-solid fa-file-lines"></i>
                            My Applications
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

                <li>
                    <a class="link" href="my_applications.php">
                        <i class="fa-solid fa-file-lines"></i>
                        My Applications
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
        </header>

        <div class="content">
            <div id="lockers">
                <!-- Lockers header -->
                <header class="searchFilter">
                    <form method="GET">
                        <div class="search-group">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="search"
                                name="searchLockerSlotLocation"
                                placeholder="Search locker locations..."
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
                
                <!-- Lockers -->
                <div class="table-container">
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

                    <?php if ($lockerSlotLocationResultSet->num_rows > 0): ?>
                        <?php while($lockerSlotLocationRow = $lockerSlotLocationResultSet->fetch_assoc()) { ?>
                            <div class="location-slot-section">
                                <div class="location-header">
                                    <h3><?= $lockerSlotLocationRow['location'] ?></h3>
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
                                                <th>Academic Year</th>
                                                <th>Start On</th>
                                                <th>End On</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php while($lockerSlotsRow = $lockerSlotResultSet->fetch_assoc()) { ?>
                                                <tr>
                                                <td data-label="Slot Number"><?= $lockerSlotsRow['slot_number'] ?></td>
                                                <td data-label="Size"><?= $lockerSlotsRow['size'] ?></td>
                                                <td data-label="Price">&#8369;<?= $lockerSlotsRow['price'] ?></td>
                                                
                                                <td data-label="Academic Year">
                                                    <?= $lockerSlotsRow['academic_year'] ?> - <?= $lockerSlotsRow['semester'] ?>
                                                </td>

                                                <td data-label="Start On">
                                                    <?= $lockerSlotsRow['start_at'] ?>
                                                </td>

                                                <td data-label="End On">
                                                    <?= $lockerSlotsRow['end_at'] ?>
                                                </td>

                                                <td data-label="Status"><?= $lockerSlotsRow['status'] ?></td>

                                                <td data-label="Action">
                                                    <div class="action-buttons">
                                                        <button 
                                                            type="button" 
                                                            class="sm-btn secondary-btn"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#applyLockerSlotModal<?= $lockerSlotsRow['id'] ?>">

                                                            <i class="fa-brands fa-jxl"></i>
                                                            Apply
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>

                                                <!-- Apply locker slot modal -->
                                                <div class="modal fade success-modal"
                                                    id="applyLockerSlotModal<?= $lockerSlotsRow['id'] ?>"
                                                    tabindex="-1">

                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">

                                                            <div class="modal-body">
                                                                <div class="message">
                                                                    <i class="fa-brands fa-jxl"></i>
                                                                    <h5>Apply</h5>
                                                                    <p>
                                                                        Are you sure you want to apply <span>slot <?= $lockerSlotsRow['slot_number'] ?></span>?
                                                                    </p>
                                                                </div>

                                                                <div class="action-buttons">
                                                                    <button type="button" class="btn secondary-btn" data-bs-dismiss="modal">
                                                                        Cancel
                                                                    </button>

                                                                    <form method="POST" action="../app/locker/apply_locker_slot.php">
                                                                        <input type="hidden" name="slot_id" value="<?= $lockerSlotsRow['id'] ?>">

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
                                        <small>Try to <a href="javascript:location.reload();"><i class="fa-solid fa-arrows-rotate"></i> reload</a> page</small>
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
                            <small>Try to <a href="javascript:location.reload();"><i class="fa-solid fa-arrows-rotate"></i> reload</a> page</small>
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