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
                        <i class="fa-solid fa-plus"></i>
                        View
                    </button>
                    
                    <ul class="dropdown-menu"> 
                        <li>
                            <button type="button" class="dropdown-item" data-bs-toggle="offcanvas" data-bs-target="#lockerApplicationOffcanvas">
                                View Locker Applications
                            </button>
                        </li>
                        <li><hr class="dropdown-divider"></hr></li>
                        <li>
                            <button type="button" class="dropdown-item" data-bs-toggle="offcanvas" data-bs-target="#lockerLocationsOffcanvas">
                                View Locker Locations
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item" data-bs-toggle="offcanvas" data-bs-target="#lockerSizesOffcanvas">
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
                                name="searchLockerLocation"
                                placeholder="Search locker locations..."
                                value="<?= htmlspecialchars($_GET['searchLockerLocation'] ?? '') ?>">
                        </div>

                        <div class="filter-group">
                            <i class="fa-solid fa-filter"></i>
                            <select name="filter" onchange="this.form.submit()">
                                <option value="">All</option>
                                <option value="asc" <?= (($_GET['filter'] ?? '') === 'asc') ? 'selected' : '' ?>>A - Z</option>
                                <option value="desc" <?= (($_GET['filter'] ?? '') === 'desc') ? 'selected' : '' ?>>Z - A</option>
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

                        <?php
                            $result = $conn_local->query("CALL getLockerSlotApplication()");
                            $conn_local->next_result();
                        ?>

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
                                        <td><?= $row['fullname'] ?></td>
                                        <td><?= $row['location'] ?></td>
                                        <td>Slot <?= $row['slot_number'] ?></td>
                                        <td><?= $row['size'] ?> (₱<?= $row['price'] ?>)</td>
                                        <td>
                                            <?php if ($row['status'] == 'Pending') { ?>
                                                <span class="badge rounded-pill bg-warning">Pending</span>

                                            <?php } elseif ($row['status'] == 'Accepted') { ?>
                                                <span class="badge rounded-pill bg-success">Accepted</span>

                                            <?php } elseif ($row['status'] == 'Revoked') { ?>
                                                <span class="badge rounded-pill bg-danger">Revoked</span>
                                            
                                            <?php } else { ?>
                                                <span class="badge rounded-pill bg-danger">Rejected</span>
                                            <?php }?>
                                        </td>

                                        <td>
                                            <?php if ($row['status'] == 'Pending') { ?>

                                                <form method="POST" action="../app/locker/accept_locker_application.php" style="display:inline;">
                                                    <input type="hidden" name="id" value="<?= $row['application_id'] ?>">

                                                    <button class="sm-btn primary-btn">
                                                        <i class="fa-solid fa-check"></i>
                                                        Accept
                                                    </button>
                                                </form>

                                                <form method="POST" action="../app/locker/reject_locker_application.php" style="display:inline;">
                                                    <input type="hidden" name="id" value="<?= $row['application_id'] ?>">

                                                    <button class="sm-btn danger-btn">
                                                        <i class="fa-solid fa-xmark"></i>
                                                        Reject
                                                    </button>
                                                </form>

                                            <?php } elseif ($row['status'] == 'Accepted') { ?>

                                                <form method="POST" action="../app/locker/revoke_locker_application.php" style="display:inline;">
                                                    <input type="hidden" name="id" value="<?= $row['application_id'] ?>">

                                                    <button class="sm-btn warning-btn">
                                                        <i class="fa-solid fa-ban"></i>
                                                        Revoke
                                                    </button>
                                                </form>

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

                <!-- Offcanvas for locker locations -->
                <div class="offcanvas offcanvas-end" id="lockerLocationsOffcanvas">
                    <div class="offcanvas-header">
                        <div>
                            <h3 class="offcanvas-title">Lockers Locations</h3>
                            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#addLockerLocationModal">
                                Add Locker Location
                            </button>
                        </div>
                        
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                    </div>

                    <div class="offcanvas-body">
                        <?php
                            $result = $conn_local->query("CALL getLockerLocations()");
                            $conn_local->next_result();
                        ?>

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
                                        <td><?= $row['location'] ?></td>

                                        <td>
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
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
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

                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="fa-solid fa-trash"></i>
                                        Delete Location
                                    </h5>
                                </div>

                                <div class="modal-body">
                                    <p>Are you sure you want to delete <i><?= $row['location'] ?></i>?</p>

                                    <div class="action-buttons">
                                        <form method="POST" action="../app/locker/delete_locker_location.php">
                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">

                                            <button type="submit" class="btn danger-btn">Yes Delete</button>
                                        </form>

                                        <button class="btn secondary-btn" data-bs-dismiss="modal">Cancel</button>
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
                        <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#addLockerSlotModal">
                            Add Locker Slot
                        </button>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                    </div>

                    <div class="offcanvas-body">
                        <?php
                            $result = $conn_local->query("CALL getLockerSizes()");
                            $conn_local->next_result();
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
                                <?php while ($row = $result->fetch_assoc()) { ?>
                                    <tr>
                                        <td><?= $row['size'] ?></td>
                                        <td><?= $row['price'] ?></td>

                                        <td>
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
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
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

                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="fa-solid fa-trash"></i>
                                        Delete Size
                                    </h5>
                                </div>

                                <div class="modal-body">
                                    <p>Are you sure you want to delete <i><?= $row['size'] ?></i>?</p>

                                    <div class="action-buttons">
                                        <form method="POST" action="../app/locker/delete_locker_size.php">
                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">

                                            <button type="submit" class="btn danger-btn">Yes Delete</button>
                                        </form>

                                        <button class="btn secondary-btn" data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                <?php } ?>

                <div class="table-container">
                    <!-- Get locker locations and search, filter -->
                    <?php
                        $search = $_GET['searchLockerLocation'] ?? '';
                        $filter = $_GET['filter'] ?? '';

                        $stmt = $conn_local->prepare("CALL getSearchFilterLockerLocation(?, ?)");
                        $stmt->bind_param("ss", $search, $filter);
                        $stmt->execute();

                        $locations = $stmt->get_result();
                        $stmt->close();

                        $conn_local->next_result();
                    ?>

                    <?php while($loc = $locations->fetch_assoc()) { ?>

                        <div class="location-slot-section">
                            <div class="location-header">
                                <h3><?= $loc['location'] ?></h3>
                                
                                <div class=action-buttons>
                                    <button type="button"
                                        class="sm-btn primary-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#addSlotModal<?= $loc['id'] ?>">
                                        <i class="fa-solid fa-plus"></i>
                                        Add Slot
                                    </button>
                                </div>
                            </div>

                            <!-- Add slot modal (per location) -->
                            <div class="modal fade primary-modal" id="addSlotModal<?= $loc['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">

                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                <i class="fa-solid fa-plus"></i>
                                                Add Slot - <?= $loc['location'] ?>
                                            </h5>
                                        </div>

                                        <div class="modal-body">
                                            <?php
                                                // get sizes only
                                                $sizes = $conn_local->query("CALL getLockerSizes()");
                                                $conn_local->next_result();
                                            ?>

                                            <form method="POST" action="../app/locker/add_locker_slot.php">
                                                <input type="hidden" name="location_id" value="<?= $loc['id'] ?>">

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

                            <!-- Update location modals -->
                            <div class="modal fade primary-modal" id="updateLockerLocationModal<?= $loc['id'] ?>" tabindex="-1">
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
                                                <input type="hidden" name="id" value="<?= $loc['id'] ?>">

                                                <div class="form-group">
                                                    <label class="input-label">Location Name</label>
                                                    <div class="input-box">
                                                        <input type="text" name="location" value="<?= $loc['location'] ?>" required>
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

                            <!-- Delete location modals -->
                            <div class="modal fade danger-modal" id="deleteLockerLocationModal<?= $loc['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">

                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                <i class="fa-solid fa-trash"></i>
                                                Delete Location
                                            </h5>
                                        </div>

                                        <div class="modal-body">
                                            <p>
                                                Are you sure you want to delete <i><?= $loc['location'] ?></i>?
                                            </p>

                                            <div class="action-buttons">
                                                <form method="POST" action="../app/locker/delete_locker_location.php">
                                                    <input type="hidden" name="id" value="<?= $loc['id'] ?>">

                                                    <button type="submit" class="btn primary-btn">
                                                        Yes, Delete
                                                    </button>
                                                </form>

                                                <button type="button" class="btn secondary-btn" data-bs-dismiss="modal">
                                                    Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php
                                // Get lockers per location
                                $stmt = $conn_local->prepare("CALL getLockersByLocation(?)");
                                $stmt->bind_param("i", $loc['id']);
                                $stmt->execute();

                                $result = $stmt->get_result();
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
                                    <?php while($row = $result->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?= $row['slot_number'] ?></td>
                                            <td><?= $row['size'] ?></td>
                                            <td><?= $row['price'] ?></td>
                                            <td><?= $row['status'] ?></td>

                                            <td>
                                                <button 
                                                    type="button" 
                                                    class="sm-btn primary-btn"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#updateLockerSlotModal<?= $row['id'] ?>">
                                                    <i class="fa-solid fa-pen"></i>
                                                    Edit
                                                </button>

                                                <button 
                                                    type="button" 
                                                    class="sm-btn danger-btn"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteLockerSlotConfirmationModal<?= $row['id'] ?>">
                                                    <i class="fa-solid fa-trash"></i>
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Update locker slot modal -->
                                        <div class="modal fade primary-modal" id="updateLockerSlotModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
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
                                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">

                                                            <div class="form-group">
                                                                <label class="input-label">Slot Number</label>
                                                                <div class="input-box">
                                                                    <input type="number" name="slot_number" value="<?= $row['slot_number'] ?>" required>
                                                                </div>
                                                            </div>

                                                            <div class="form-group">
                                                                <label class="input-label">Status</label>
                                                                <div class="input-box">
                                                                    <select name="status">
                                                                        <option value="Available" <?= $row['status'] == 'Available' ? 'selected' : '' ?>>Available</option>
                                                                        <option value="Occupied" <?= $row['status'] == 'Occupied' ? 'selected' : '' ?>>Occupied</option>
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
                                        <div class="modal fade danger-modal" id="deleteLockerSlotConfirmationModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">

                                                    <div class="modal-header">
                                                        <h5 class="modal-title">
                                                            <i class="fa-solid fa-trash"></i>
                                                            Delete Confirmation
                                                        </h5>
                                                    </div>

                                                    <div class="modal-body">
                                                        <p>Are you sure you want to delete slot <i><?= $row['slot_number'] ?></i>?</p>

                                                        <div class="action-buttons">

                                                            <form method="POST" action="../app/locker/delete_locker_slot.php">
                                                                <input type="hidden" name="id" value="<?= $row['id'] ?>">

                                                                <button type="submit" class="btn primary-btn">
                                                                    Yes, Delete
                                                                </button>
                                                            </form>

                                                            <button type="button" class="btn secondary-btn" data-bs-dismiss="modal">
                                                                Cancel
                                                            </button>

                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </tbody>
                            </table>

                            <?php
                                $stmt->close();
                                $conn_local->next_result();
                            ?>
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

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        Logout Confirmation
                    </h5>
                </div>

                <div class="modal-body">
                    <p>Are you sure you want to logout?</p>
                    
                    <div class="action-buttons">
                        <a href="../app/auth/logout.php" class="btn primary-btn">
                            Yes, Logout
                        </a>
                        
                        <button type="button" class="btn secondary-btn" data-bs-dismiss="modal">
                            No
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

<!-- Js -->
<script src="../assets/js/script.js"></script>
