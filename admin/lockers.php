<?php
session_start();
require '../app/db_connection.php';

if (!isset($_SESSION['id']) || !isset($_SESSION['email'])) {
    header('Location: ../index.php');
    exit;
}

// Set session variables
$id = $_SESSION['id'] ?? '';

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
                <a href="dashboard.php">
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
                            logout
                        </a>
                    </li>
                </div>
            </ul>
        </div>
    </nav>

    <!-- Desktop Sidebar -->
    <nav id="sidebarDesktop">
        <ul class="nav">
            <a href="dashboard.php">
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
                        logout
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
                                        name="searchApplication"
                                        placeholder="Search locker applications..."
                                        value="<?= htmlspecialchars($_GET['searchApplication'] ?? '') ?>">
                                </div>

                                <div class="filter-group">
                                    <i class="fa-solid fa-filter"></i>
                                    <select name="filterApplication" onchange="this.form.submit()">
                                        <option value="">All</option>
                                        <option value="asc" <?= (($_GET['filterApplication'] ?? '') === 'asc') ? 'selected' : '' ?>>A - Z</option>
                                        <option value="desc" <?= (($_GET['filterApplication'] ?? '') === 'desc') ? 'selected' : '' ?>>Z - A</option>
                                    </select>
                                </div>

                                <button type="submit" hidden></button>
                            </form>
                        </header>

                        <?php
                            $result = $conn_local->query("CALL getLockerSlotApplication()");
                            $conn_local->next_result();
                        ?>

                        <div class="table-container">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Location</th>
                                        <th>Slot</th>
                                        <th>Size & Price</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()) { ?>
                                        <tr>
                                            <td data-label="Full Name"><?= $row['fullname'] ?></td>
                                            <td data-label="Location"><?= $row['location'] ?></td>
                                            <td data-label="Slot">Slot <?= $row['slot_number'] ?></td>
                                            <td data-label="Details"><?= $row['size'] ?> (₱<?= $row['price'] ?>)</td>

                                            <td data-label="Status">
                                                <?php if ($row['status'] == 'Pending') { ?>
                                                    <span class="badge rounded-pill pending-badge">Pending</span>

                                                <?php } elseif ($row['status'] == 'Accepted') { ?>
                                                    <span class="badge rounded-pill accepted-badge">Accepted</span>

                                                <?php } elseif ($row['status'] == 'Revoked') { ?>
                                                    <span class="badge rounded-pill revoked-badge">Revoked</span>

                                                <?php } elseif ($row['status'] == 'Cancelled') { ?>
                                                    <span class="badge rounded-pill cancelled-badge">Cancelled</span>

                                                <?php } else { ?>
                                                    <span class="badge rounded-pill rejected-badge">Rejected</span>
                                                <?php } ?>
                                            </td>

                                            <td data-label="Action">
                                                <?php if ($row['status'] == 'Pending') { ?> 
                                                    <div class="action-buttons">
                                                        <button class="sm-btn primary-btn"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#acceptLockerSlotApplicationModal<?= $row['application_id'] ?>">
                                                            <i class="fa-solid fa-check"></i> Accept
                                                        </button>

                                                        <button class="sm-btn danger-btn"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#rejectLockerSlotApplicationModal<?= $row['application_id'] ?>">
                                                            <i class="fa-solid fa-xmark"></i> Reject
                                                        </button>
                                                    </div>
                                                <?php } elseif ($row['status'] == 'Accepted') { ?>
                                                    <div class="action-buttons">
                                                        <button class="sm-btn danger-btn"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#revokeLockerSlotApplicationModal<?= $row['application_id'] ?>">
                                                            <i class="fa-solid fa-ban"></i> Revoke
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
                    $result = $conn_local->query("CALL getLockerSlotApplication()");
                    $conn_local->next_result();

                    while ($row = $result->fetch_assoc()) {
                ?>

                    <!-- Accept locker slot application modals -->
                    <div class="modal fade primary-modal" id="acceptLockerSlotApplicationModal<?= $row['application_id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div class="message">
                                        <i class="fa-solid fa-circle-check"></i>
                                        <h5>Accept</h5>
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

                    <!-- Reject locker slot application modals -->
                    <div class="modal fade danger-modal" id="rejectLockerSlotApplicationModal<?= $row['application_id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div class="message">
                                        <i class="fa-solid fa-circle-xmark"></i>
                                        <h5>Reject</h5>
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

                    <!-- Revoke locker slot application modals -->
                    <div class="modal fade danger-modal" id="revokeLockerSlotApplicationModal<?= $row['application_id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div class="message">
                                        <i class="fa-solid fa-ban"></i>
                                        <h5>Revoke</h5>
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

                        <?php
                            $result = $conn_local->query("CALL getLockerLocations()");
                            $conn_local->next_result();
                        ?>

                        <div class="table-container">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th>Location</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()) { ?>
                                        <tr>
                                            <td data-label="Location"><?= $row['location'] ?></td>

                                            <td data-label="Action">
                                                <div class="action-buttons">
                                                    <button class="sm-btn primary-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#updateLockerLocation<?= $row['id'] ?>">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                        Edit
                                                    </button>

                                                    <button class="sm-btn danger-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteLockerLocation<?= $row['id'] ?>">
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
                    $result = $conn_local->query("CALL getLockerLocations()");
                    $conn_local->next_result();

                    while ($row = $result->fetch_assoc()) {
                ?>

                    <!-- Update locker location modal -->
                    <div class="modal fade primary-modal" id="updateLockerLocation<?= $row['id'] ?>" tabindex="-1">
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
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">

                                        <div class="form-group">
                                            <label class="input-label">Location</label>
                                            <div class="input-box">
                                                <input type="text" name="location" value="<?= $row['location'] ?>" required>
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
                    <div class="modal fade danger-modal" id="deleteLockerLocation<?= $row['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div class="message">
                                        <p><i class="fa-solid fa-circle-exclamation"></i></p>
                                        <h5>Delete</h5>
                                        <p>Are you sure you want to delete <span><?= $row['location'] ?></span>?</p>
                                    </div>

                                    <div class="action-buttons">
                                        <button class="btn secondary-btn" data-bs-dismiss="modal">Cancel</button>

                                        <form method="POST" action="../app/locker/delete_locker_location.php">
                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
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
                                        name="searchSizes"
                                        placeholder="Search locker sizes..."
                                        value="<?= htmlspecialchars($_GET['searchSizes'] ?? '') ?>">
                                </div>

                                <div class="filter-group">
                                    <i class="fa-solid fa-filter"></i>
                                    <select name="filterSizes" onchange="this.form.submit()">
                                        <option value="">All</option>
                                        <option value="asc" <?= (($_GET['filterSizes'] ?? '') === 'asc') ? 'selected' : '' ?>>A - Z</option>
                                        <option value="desc" <?= (($_GET['filterSizes'] ?? '') === 'desc') ? 'selected' : '' ?>>Z - A</option>
                                    </select>
                                </div>

                                <button type="submit" hidden></button>
                            </form>
                            <button type="button" class="btn primary-btn" data-bs-toggle="modal" data-bs-target="#addLockerSizeModal">
                                <i class="fa-solid fa-plus"></i>
                                Add Locker Size 
                            </button>
                        </header>

                        <?php
                            $result = $conn_local->query("CALL getLockerSizes()");
                            $conn_local->next_result();
                        ?>

                        <div class="table-container">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th>Size</th>
                                        <th>Price</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()) { ?>
                                        <tr>
                                            <td data-label="Size"><?= $row['size'] ?></td>
                                            <td data-label="Price"><?= $row['price'] ?></td>

                                            <td data-label="Action">
                                                <div class="action-buttons">
                                                    <button class="sm-btn primary-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#updateLockerSize<?= $row['id'] ?>">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                        Edit
                                                    </button>

                                                    <button class="sm-btn danger-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteLockerSize<?= $row['id'] ?>">
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
                    $result = $conn_local->query("CALL getLockerSizes()");
                    $conn_local->next_result();

                    while ($row = $result->fetch_assoc()) {
                ?>
                    <!-- Update locker size modal -->
                    <div class="modal fade primary-modal" id="updateLockerSize<?= $row['id'] ?>" tabindex="-1">
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
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">

                                        <div class="form-group">
                                            <label class="input-label">Size</label>
                                            <div class="input-box">
                                                <input type="text" name="size" value="<?= $row['size'] ?>" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="input-label">Type</label>
                                            <div class="input-box">
                                                <input type="number" step="0.01" name="price" value="<?= $row['price'] ?>" required>
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
                    <div class="modal fade danger-modal" id="deleteLockerSize<?= $row['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div class="message">
                                        <p><i class="fa-solid fa-circle-exclamation"></i></p>
                                        <h5>Delete</h5>
                                        <p>Are you sure you want to delete <span><?= $row['size'] ?></span>?</p>
                                    </div>

                                    <div class="action-buttons">
                                        <button class="btn secondary-btn" data-bs-dismiss="modal">Cancel</button>

                                        <form method="POST" action="../app/locker/delete_locker_size.php">
                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">

                                            <button type="submit" class="btn danger-btn">Yes, Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <div class="table-container">
                    <?php
                        // Get locker locations
                        $search = trim($_GET['searchLocation'] ?? '');
                        $filter = strtolower($_GET['filterLocation'] ?? '');

                        if (!empty($search) || !empty($filter)) {
                            // Use search and filter
                            $stmtLocation = $conn_local->prepare("CALL getSearchFilterLockerLocation(?, ?)");
                            $stmtLocation->bind_param("ss", $search, $filter);
                        } else {
                            // Use raw
                            $stmtLocation = $conn_local->prepare("CALL getLockerLocations()");
                        }

                        $stmtLocation->execute();
                        $locationResultSet = $stmtLocation->get_result();
                        $stmtLocation->close();

                        $conn_local->next_result();
                        $conn_local->store_result();
                    ?>

                    <?php while ($locationRow = $locationResultSet->fetch_assoc()) { ?>
                        <div class="location-slot-section">
                            <div class="location-header">
                                <h3><?= $locationRow['location'] ?></h3>

                                <div class="action-buttons">
                                    <button type="button"
                                        class="sm-btn primary-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#addSlotModal<?= $locationRow['id'] ?>">
                                        <i class="fa-solid fa-plus"></i>
                                        Add Slot
                                    </button>
                                </div>
                            </div>

                            <!-- Add slot modal (per location) -->
                            <div class="modal fade primary-modal" id="addSlotModal<?= $locationRow['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">

                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                <i class="fa-solid fa-plus"></i>
                                                Add Slot - <?= $locationRow['location'] ?>
                                            </h5>
                                        </div>

                                        <div class="modal-body">
                                            <?php
                                                // get sizes only
                                                $sizes = $conn_local->query("CALL getLockerSizes()");
                                                $conn_local->next_result();
                                            ?>

                                            <form method="POST" action="../app/locker/add_locker_slot.php">
                                                <input type="hidden" name="location_id" value="<?= $locationRow['id'] ?>">

                                                <div class="form-group">
                                                    <label class="input-label">Size</label>
                                                    <div class="input-box">
                                                        <select name="size_id" required>
                                                            <option value="">Select Size</option>
                                                            <?php while($size = $sizes->fetch_assoc()) { ?>
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
                                $stmtSlot = $conn_local->prepare("CALL getLockersByLocation(?)");
                                $stmtSlot->bind_param("i", $locationRow['id']);
                                $stmtSlot->execute();
                                $slotResultSet = $stmtSlot->get_result();
                                $stmtSlot->close();

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
                                    <?php while ($slotRow = $slotResultSet->fetch_assoc()) { ?>
                                        <tr>
                                            <td data-label="Slot Number"><?= $slotRow['slot_number'] ?></td>
                                            <td data-label="Size"><?= $slotRow['size'] ?></td>
                                            <td data-label="Price"><?= $slotRow['price'] ?></td>
                                            <td data-label="Status"><?= $slotRow['status'] ?></td>

                                            <td data-label="Action">
                                                <div class="action-buttons">
                                                    <button type="button"
                                                        class="sm-btn primary-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#updateLockerSlotModal<?= $slotRow['id'] ?>">
                                                        <i class="fa-solid fa-pen"></i>
                                                        Edit
                                                    </button>

                                                    <button type="button"
                                                        class="sm-btn danger-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteLockerSlotConfirmationModal<?= $slotRow['id'] ?>">
                                                        <i class="fa-solid fa-trash"></i>
                                                        Delete
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Update locker slot modal -->
                                        <div class="modal fade primary-modal" id="updateLockerSlotModal<?= $slotRow['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">

                                                    <div class="modal-header">
                                                        <h5 class="modal-title">
                                                            <i class="fa-solid fa-pen-to-square"></i>
                                                            Update Locker Slot
                                                        </h5>
                                                    </div>

                                                    <div class="modal-body">
                                                        <form method="POST" action="../app/locker/update_locker_slot.php">
                                                            <input type="hidden" name="id" value="<?= $slotRow['id'] ?>">

                                                            <div class="form-group">
                                                                <label class="input-label">Slot Number</label>
                                                                <div class="input-box">
                                                                    <input type="number" name="slot_number" value="<?= $slotRow['slot_number'] ?>" required>
                                                                </div>
                                                            </div>

                                                            <div class="form-group">
                                                                <label class="input-label">Status</label>
                                                                <div class="input-box">
                                                                    <select name="status">
                                                                        <option value="Available" <?= $slotRow['status'] == 'Available' ? 'selected' : '' ?>>Available</option>
                                                                        <option value="Occupied" <?= $slotRow['status'] == 'Occupied' ? 'selected' : '' ?>>Occupied</option>
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

                                        <!-- Delete locker slot confirmation modal -->
                                        <div class="modal fade danger-modal" id="deleteLockerSlotConfirmationModal<?= $slotRow['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-body">
                                                        <div class="message">
                                                            <p><i class="fa-solid fa-circle-exclamation"></i></p>
                                                            <h5>Delete</h5>
                                                            <p>Are you sure you want to delete <span>slot <?= $slotRow['slot_number'] ?></span>?</p>
                                                        </div>

                                                         <div class="action-buttons">
                                                            <button type="button" class="btn secondary-btn" data-bs-dismiss="modal">
                                                                Cancel
                                                            </button>

                                                            <form method="POST" action="../app/locker/delete_locker_slot.php">
                                                                <input type="hidden" name="id" value="<?= $slotRow['id'] ?>">

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

    <!-- Add locker slot modal -->
    <div class="modal fade primary-modal" id="addLockerSlotModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa-solid fa-plus"></i>
                        Add Locker Slot
                    </h5>
                </div>

                <div class="modal-body">
                    <?php
                        // Get locker locations
                        $locations = $conn_local->query("CALL getLockerLocations()");
                        $conn_local->next_result();

                        // Get locker sizes
                        $sizes = $conn_local->query("CALL getLockerSizes()");
                        $conn_local->next_result();
                    ?>

                    <form method="POST" action="../app/locker/add_locker_slot.php">

                        <div class="form-group">
                            <label class="input-label">Location</label>
                            <div class="input-box">
                                <select name="location_id" required>
                                    <option value="">Select Location</option>
                                    <?php while($row = $locations->fetch_assoc()) { ?>
                                        <option value="<?= $row['id'] ?>">
                                            <?= $row['location'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">Size</label>
                            <div class="input-box">
                                 <select name="size_id" required>
                                    <option value="">Select Size</option>
                                    <?php while($row = $sizes->fetch_assoc()) { ?>
                                        <option value="<?= $row['id'] ?>">
                                            <?= $row['size'] ?>
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
                            <button type="submit" class="btn primary-btn">Add Locker Slot</button>
                            <button type="button" class="btn secondary-btn" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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
