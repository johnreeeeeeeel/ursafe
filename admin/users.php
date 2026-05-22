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

    <title>UrSafe - Users</title>

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
                        <a class="link active" href="users.php">
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
                    <a class="link active" href="users.php">
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
                <h1 class="page-title">Users</h1> 
            </div>

            <div class="right">
                <div class="dropdown">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                        View
                    </button>
                    
                    <ul class="dropdown-menu"> 
                        <li>
                            <button type="button" class="dropdown-item" data-bs-toggle="offcanvas" data-bs-target="#userAccountLogsOffcanvas" onclick="window.location.hash='userAccountLogsOffcanvas';">
                                Account Logs
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <div class="content">
            <div id="users">
                <!-- Offcanvas for user logs -->
                <div class="offcanvas offcanvas-end" id="userAccountLogsOffcanvas">
                    <?php
                        // Get user account logs
                        $limit = 16;
                        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

                        if ($page < 1) $page = 1;

                        $offset = ($page - 1) * $limit;

                        $stmtUserAccountLogs = $conn_local->prepare("CALL getUserAccountLogs(?, ?)");
                        $stmtUserAccountLogs->bind_param("ii", $limit, $offset);

                        $stmtUserAccountLogs->execute();

                        $userAccountLogsResultSet = $stmtUserAccountLogs->get_result();

                        $stmtUserAccountLogs->next_result();
                        $totalUserLogsRow = $stmtUserAccountLogs->get_result()->fetch_assoc()['userLogsTotal'];

                        $totalPages = ceil($totalUserLogsRow / $limit);

                        $stmtUserAccountLogs->close();

                        while ($conn_local->next_result()) {
                            $conn_local->store_result();
                        }
                    ?>

                    <div class="offcanvas-header">
                        <h3 class="offcanvas-title">User Account Logs</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                    </div>

                    <div class="offcanvas-body">
                        <div class="table-container">
                            <?php if ($userAccountLogsResultSet->num_rows > 0): ?>
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th>Action</th>
                                            <th>Timestamp</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php while ($userAccountLogsRow = $userAccountLogsResultSet->fetch_assoc()) { ?>
                                            <tr>
                                                <td data-label="Action">
                                                    <?php if ($userAccountLogsRow['action'] == 'Activate') { ?>
                                                        <span class="badge rounded-pill primary-badge">
                                                            <?= $userAccountLogsRow['action'] ?>
                                                        </span>
                                                    <?php } else { ?>
                                                        <span class="badge rounded-pill danger-badge">
                                                            <?= $userAccountLogsRow['action'] ?>
                                                        </span>
                                                    <?php } ?>
                                                </td>

                                                <td data-label="Timestamp"><?= $userAccountLogsRow['created_at'] ?></td>
                                                <td data-label="Description"><?= $userAccountLogsRow['description'] ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div id="empty">
                                    <i class="fa-solid fa-ban"></i>
                                    <small>No user logs yet</small>
                                    <small>Try to reload page</small>
                                </div>
                            <?php endif; ?>
                        </div>

                        <ul class="pagination">
                            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                <?php if ($page > 1): ?>
                                    <a class="page-link" href="?page=<?= $page - 1 ?>#userAccountLogsOffcanvas">Previous</a>
                                <?php else: ?>
                                    <span class="page-link">Previous</span>
                                <?php endif; ?>
                            </li>

                            <li class="page-item active">
                                <span class="page-link"><?= $page ?></span>
                            </li>

                            <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                                <?php if (mysqli_num_rows($userAccountLogsResultSet) == $limit): ?>
                                    <a class="page-link" href="?page=<?= $page + 1 ?>#userAccountLogsOffcanvas">Next</a>
                                <?php else: ?>
                                    <span class="page-link">Next</span>
                                <?php endif; ?>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Users header -->
                <header>
                    <form method="GET">
                        <div class="search-group">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="search"
                                name="searchUsers"
                                placeholder="Search users..."
                                value="<?= htmlspecialchars($_GET['searchUsers'] ?? '') ?>">
                        </div>

                        <div class="filter-group">
                            <i class="fa-solid fa-filter"></i>
                            <select name="filterUsers" onchange="this.form.submit()">
                                <option value="">All</option>
                                <option value="a-z" <?= (($_GET['filterUsers'] ?? '') === 'a-z') ? 'selected' : '' ?>>A - Z</option>
                                <option value="z-a" <?= (($_GET['filterUsers'] ?? '') === 'z-a') ? 'selected' : '' ?>>Z - A</option>
                                <option value="newest" <?= (($_GET['filterUsers'] ?? '') === 'newest') ? 'selected' : '' ?>>Newest</option>
                                <option value="oldest" <?= (($_GET['filterUsers'] ?? '') === 'oldest') ? 'selected' : '' ?>>Oldest</option>
                            </select>
                        </div>

                        <button type="submit" hidden></button>
                    </form>
                </header>

                <!-- Users -->
                <div class="table-container">
                    <?php
                        // Get users
                        $limit = 12;
                        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                        if ($page < 1) $page = 1;

                        $offset = ($page - 1) * $limit;

                        $search = trim($_GET['searchUsers'] ?? '');
                        $filter = strtolower($_GET['filterUsers'] ?? '');

                        $isSearching = !empty($search);
                        $isFiltering = ($filter !== '');
                        $isSearchFilterMode = $isSearching || $isFiltering;

                        if ($isSearchFilterMode) {
                            $stmtUsers = $conn_local->prepare("CALL getSearchFilterUsers(?, ?, ?, ?)");
                            $stmtUsers->bind_param("ssii", $search, $filter, $limit, $offset);
                        } else {
                            $stmtUsers = $conn_local->prepare("CALL getUsers(?, ?)");
                            $stmtUsers->bind_param("ii", $limit, $offset);
                        }

                        $stmtUsers->execute();

                        $usersResultSet = $stmtUsers->get_result();

                        $stmtUsers->next_result();
                        $countResult = $stmtUsers->get_result();

                        $rowCount = $countResult ? $countResult->fetch_assoc() : null;
                        $totalUsersRow = $rowCount['usersTotal'] ?? 0;

                        $totalPages = ceil($totalUsersRow / $limit);

                        $stmtUsers->close();

                        while ($conn_local->next_result()) {
                            $conn_local->store_result();
                        }
                    ?>

                    <?php if ($usersResultSet->num_rows > 0): ?>
                        <table class="table table-borderless">
                            <thead>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Fullname</th>
                                <th>Email</th>
                                <th>Action</th>
                            </thead>
                            
                            <tbody>
                                <?php while ($usersRow = $usersResultSet->fetch_assoc()): ?>
                                    <tr>
                                    <td data-label="ID"><?= $usersRow['id']; ?></td>
                                    <td data-label="Username"><?= $usersRow['username']; ?></td>
                                    <td data-label="Full Name"><?= $usersRow['fullname']; ?></td>
                                    <td data-label="Email"><?= $usersRow['email']; ?></td>

                                    <td data-label="Action">
                                        <div class="action-buttons">
                                            <button class="sm-btn primary-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#viewUserModal"
                                                onclick="viewUserDetails(
                                                    '<?= htmlspecialchars($usersRow['id']) ?>',
                                                    '<?= htmlspecialchars($usersRow['fullname']) ?>',
                                                    '<?= htmlspecialchars($usersRow['sex'] ?? '') ?>',
                                                    '<?= htmlspecialchars($usersRow['dob'] ?? '') ?>',
                                                    '<?= htmlspecialchars($usersRow['institute'] ?? '') ?>',
                                                    '<?= htmlspecialchars($usersRow['program'] ?? '') ?>',
                                                    '<?= htmlspecialchars($usersRow['username']) ?>',
                                                    '<?= htmlspecialchars($usersRow['email']) ?>',
                                                    '<?= htmlspecialchars($usersRow['created_at']) ?>'
                                                )">

                                                <i class="fa-solid fa-eye"></i>
                                                View
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        <ul class="pagination">
                            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                <?php if ($page > 1): ?>
                                    <a class="page-link"
                                    href="?page=<?= $page - 1 ?>&searchUsers=<?= urlencode($_GET['searchUsers'] ?? '') ?>&filterUsers=<?= urlencode($_GET['filterUsers'] ?? '') ?>">
                                        Previous
                                    </a>
                                <?php else: ?>
                                    <span class="page-link">Previous</span>
                                <?php endif; ?>
                            </li>

                            <li class="page-item active">
                                <span class="page-link"><?= $page ?></span>
                            </li>

                            <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                                <?php if ($page < $totalPages): ?>
                                    <a class="page-link"
                                    href="?page=<?= $page + 1 ?>&searchUsers=<?= urlencode($_GET['searchUsers'] ?? '') ?>&filterUsers=<?= urlencode($_GET['filterUsers'] ?? '') ?>">
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
                            <small>No activated user yet</small>
                            <small>Try to reload page</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- View user details modal -->
        <div class="modal fade" id="viewUserModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="profile">
                            <i class="fa-solid fa-circle-user"></i>
                            <h4><span id="vu_fullname"></span></h4>
                        </div>
                        
                        
                        <div class="profile-section">
                            <h6>Personal Information</h6>
                            <p>
                                <small><b>ID: </b><span id="vu_id"></span></small>
                                <small><b>Username: </b><span id="vu_username"></span></small>
                            </p>
                            <p>
                                <small><b>Sex: </b><span id="vu_sex"></span></small>
                                <small><b>Date of Birth: </b><span id="vu_dob"></span></small>
                            </p>
                        </div>

                        <div class="profile-section">
                            <h6>Academic Information</h6>
                            <p>
                                <small><b>Institute: </b><span id="vu_institute"></span></small>
                            </p>
                            <p>
                                <small><b>Program: </b><span id="vu_program"></span></small>
                            </p>
                        </div>

                        <div class="profile-section">
                            <h6>Account Information</h6>
                            <p>
                                <small><b>Email: </b><span id="vu_email"></span></small>
                            </p>
                            <p>
                                <small><b>Date Activated: </b><span id="vu_created_at"></span></small>
                            </p>
                        </div>
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
    </section>
</body>

<!-- Js -->
<script src="../assets/js/script.js"></script>
