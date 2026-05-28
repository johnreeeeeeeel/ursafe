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

                                                    <?php } elseif ($userAccountLogsRow['action'] == 'Deactivate') { ?>
                                                        <span class="badge rounded-pill danger-badge">
                                                            <?= $userAccountLogsRow['action'] ?>
                                                        </span>
                                                    
                                                    <?php } elseif ($userAccountLogsRow['action'] == 'Add') { ?>
                                                        <span class="badge rounded-pill primary-badge">
                                                            <?= $userAccountLogsRow['action'] ?>
                                                        </span>

                                                    <?php } elseif ($userAccountLogsRow['action'] == 'Update') { ?>
                                                        <span class="badge rounded-pill warning-badge">
                                                            <?= $userAccountLogsRow['action'] ?>
                                                        </span>

                                                    <?php } elseif ($userAccountLogsRow['action'] == 'Delete') { ?>
                                                        <span class="badge rounded-pill danger-badge">
                                                            <?= $userAccountLogsRow['action'] ?>
                                                        </span>

                                                    <?php } else { ?>
                                                        <span class="badge rounded-pill secondary-badge">
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

                <!-- Users -->
                <div class="table-container">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link <?= ($_GET['tab'] ?? 'activeUsersTab') === 'activeUsersTab' ? 'active' : '' ?>" href="?tab=activeUsersTab">
                                Active Users
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link <?= ($_GET['tab'] ?? '') === 'inactiveUsersTab' ? 'active' : '' ?>" href="?tab=inactiveUsersTab">
                                Inactive Users
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Active users -->
                        <div class="tab-pane fade <?= ($_GET['tab'] ?? 'activeUsersTab') === 'activeUsersTab' ? 'show active' : '' ?>" id="activeUsersTab">
                            <?php
                                // Get users
                                $limit = 8;
                                $activeUsersPage = isset($_GET['activeUsersPage']) ? (int)$_GET['activeUsersPage'] : 1;
                                if ($activeUsersPage < 1) $activeUsersPage = 1;

                                $offset = ($activeUsersPage - 1) * $limit;

                                $search = trim($_GET['searchActiveUsers'] ?? '');
                                $filter = strtolower($_GET['filterActiveUsers'] ?? '');

                                $isSearching = !empty($search);
                                $isFiltering = ($filter !== '');
                                $isSearchFilterMode = $isSearching || $isFiltering;

                                if ($isSearchFilterMode) {
                                    $stmtActiveUsers = $conn_local->prepare("CALL getSearchFilterActiveUsers(?, ?, ?, ?)");
                                    $stmtActiveUsers->bind_param("ssii", $search, $filter, $limit, $offset);
                                } else {
                                    $stmtActiveUsers = $conn_local->prepare("CALL getActiveUsers(?, ?)");
                                    $stmtActiveUsers->bind_param("ii", $limit, $offset);
                                }

                                $stmtActiveUsers->execute();
                                $activeUsersResultSet = $stmtActiveUsers->get_result();

                                $stmtActiveUsers->next_result();
                                $activeUsersCountResult = $stmtActiveUsers->get_result();

                                $activeUsersRowCount = $activeUsersCountResult ? $activeUsersCountResult->fetch_assoc() : null;
                                $totalActiveUsersRow = $activeUsersRowCount['activeUsersTotal'] ?? 0;

                                $totalActiveUsersPages = ceil($totalActiveUsersRow / $limit);

                                $stmtActiveUsers->close();

                                while ($conn_local->next_result()) {
                                    $conn_local->store_result();
                                }
                            ?>

                            <header>
                                <form method="GET">
                                    <input type="hidden" name="tab" value="activeUsersTab">

                                    <div class="search-group">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                        <input type="search"
                                            name="searchActiveUsers"
                                            placeholder="Search users..."
                                            value="<?= htmlspecialchars($_GET['searchActiveUsers'] ?? '') ?>">
                                    </div>

                                    <div class="filter-group">
                                        <i class="fa-solid fa-filter"></i>
                                        <select name="filterActiveUsers" onchange="this.form.submit()">
                                            <option value="">All</option>
                                            <option value="a-z" <?= (($_GET['filterActiveUsers'] ?? '') === 'a-z') ? 'selected' : '' ?>>A - Z</option>
                                            <option value="z-a" <?= (($_GET['filterActiveUsers'] ?? '') === 'z-a') ? 'selected' : '' ?>>Z - A</option>
                                            <option value="newest" <?= (($_GET['filterActiveUsers'] ?? '') === 'newest') ? 'selected' : '' ?>>Newest</option>
                                            <option value="oldest" <?= (($_GET['filterActiveUsers'] ?? '') === 'oldest') ? 'selected' : '' ?>>Oldest</option>
                                        </select>
                                    </div>

                                    <button type="submit" hidden></button>
                                </form>
                            </header>

                            <?php if ($activeUsersResultSet->num_rows > 0): ?>
                                <table class="table table-borderless">
                                    <thead>
                                        <th>ID</th>
                                        <th>Status</th>
                                        <th>Username</th>
                                        <th>Fullname</th>
                                        <th>Email</th>
                                        <th>Action</th>
                                    </thead>
                                    
                                    <tbody>
                                        <?php while ($activeUsersRow = $activeUsersResultSet->fetch_assoc()): ?>
                                            <tr>
                                            <td data-label="ID"><?= $activeUsersRow['id']; ?></td>
                                            <td data-label="Status"><?= $activeUsersRow['status']; ?></td>
                                            <td data-label="Username"><?= $activeUsersRow['username']; ?></td>
                                            <td data-label="Full Name"><?= $activeUsersRow['fullname']; ?></td>
                                            <td data-label="Email"><?= $activeUsersRow['email']; ?></td>

                                            <td data-label="Action">
                                                <div class="action-buttons">
                                                    <button class="sm-btn primary-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#viewActiveUserModal"
                                                        onclick="viewActiveUserDetails(
                                                            '<?= htmlspecialchars($activeUsersRow['id']) ?>',
                                                            '<?= htmlspecialchars($activeUsersRow['fullname']) ?>',
                                                            '<?= htmlspecialchars($activeUsersRow['sex'] ?? '') ?>',
                                                            '<?= htmlspecialchars($activeUsersRow['dob'] ?? '') ?>',
                                                            '<?= htmlspecialchars($activeUsersRow['institute'] ?? '') ?>',
                                                            '<?= htmlspecialchars($activeUsersRow['program'] ?? '') ?>',
                                                            '<?= htmlspecialchars($activeUsersRow['status'] ?? '') ?>',
                                                            '<?= htmlspecialchars($activeUsersRow['username']) ?>',
                                                            '<?= htmlspecialchars($activeUsersRow['email']) ?>',
                                                            '<?= htmlspecialchars($activeUsersRow['updated_at']) ?>',
                                                            '<?= htmlspecialchars($activeUsersRow['created_at']) ?>'
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
                                    <li class="page-item <?= ($activeUsersPage <= 1) ? 'disabled' : '' ?>">
                                        <?php if ($activeUsersPage > 1): ?>
                                            <a class="page-link"
                                                href="?tab=activeUsersTab&activeUsersPage=<?= $activeUsersPage - 1 ?>&searchActiveUsers=<?= urlencode($_GET['searchActiveUsers'] ?? '') ?>&filterActiveUsers=<?= urlencode($_GET['filterActiveUsers'] ?? '') ?>"
                                                Previous
                                            </a>
                                        <?php else: ?>
                                            <span class="page-link">Previous</span>
                                        <?php endif; ?>
                                    </li>

                                    <li class="page-item active">
                                        <span class="page-link"><?= $activeUsersPage ?></span>
                                    </li>

                                    <li class="page-item <?= ($activeUsersPage >= $totalActiveUsersPages) ? 'disabled' : '' ?>">
                                        <?php if ($activeUsersPage < $totalActiveUsersPages): ?>
                                            <a class="page-link"
                                                href="?tab=activeUsersTab&activeUsersPage=<?= $activeUsersPage - 1 ?>&searchActiveUsers=<?= urlencode($_GET['searchActiveUsers'] ?? '') ?>&filterActiveUsers=<?= urlencode($_GET['filterActiveUsers'] ?? '') ?>"
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

                        <!-- Inactive users -->
                        <div class="tab-pane fade <?= ($_GET['tab'] ?? '') === 'inactiveUsersTab' ? 'show active' : '' ?>" id="inactiveUsersTab">
                            <?php
                                // Get users
                                $limit = 8;
                                $inactiveUsersPage = isset($_GET['inactiveUsersPage']) ? (int)$_GET['inactiveUsersPage'] : 1;
                                if ($inactiveUsersPage < 1) $inactiveUsersPage = 1;

                                $offset = ($inactiveUsersPage - 1) * $limit;

                                $search = trim($_GET['searchInactiveUsers'] ?? '');
                                $filter = strtolower($_GET['searchInactiveUsers'] ?? '');

                                $isSearching = !empty($search);
                                $isFiltering = ($filter !== '');
                                $isSearchFilterMode = $isSearching || $isFiltering;

                                if ($isSearchFilterMode) {
                                    $stmtInctiveUsers = $conn_local->prepare("CALL getSearchFilterInactiveUsers(?, ?, ?, ?)");
                                    $stmtInctiveUsers->bind_param("ssii", $search, $filter, $limit, $offset);
                                } else {
                                    $stmtInctiveUsers = $conn_local->prepare("CALL getInactiveUsers(?, ?)");
                                    $stmtInctiveUsers->bind_param("ii", $limit, $offset);
                                }

                                $stmtInctiveUsers->execute();
                                $inactiveUsersResultSet = $stmtInctiveUsers->get_result();

                                $stmtInctiveUsers->next_result();
                                $inactiveUsersCountResult = $stmtInctiveUsers->get_result();

                                $inactiveUsersRowCount = $inactiveUsersCountResult ? $activeUsersCountResult->fetch_assoc() : null;
                                $totalInactiveUsersRow = $inactiveUsersRowCount['inactiveUsersTotal'] ?? 0;

                                $totalInactiveUsersPages = ceil($totalInactiveUsersRow / $limit);

                                $stmtInctiveUsers->close();

                                while ($conn_local->next_result()) {
                                    $conn_local->store_result();
                                }
                            ?>

                            <header>
                                <form method="GET">
                                    <input type="hidden" name="tab" value="inactiveUsersTab">

                                    <div class="search-group">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                        <input type="search"
                                            name="searchInactiveUsers"
                                            placeholder="Search users..."
                                            value="<?= htmlspecialchars($_GET['searchInactiveUsers'] ?? '') ?>">
                                    </div>

                                    <div class="filter-group">
                                        <i class="fa-solid fa-filter"></i>
                                        <select name="filterInactiveUsers" onchange="this.form.submit()">
                                            <option value="">All</option>
                                            <option value="a-z" <?= (($_GET['filterInactiveUsers'] ?? '') === 'a-z') ? 'selected' : '' ?>>A - Z</option>
                                            <option value="z-a" <?= (($_GET['filterInactiveUsers'] ?? '') === 'z-a') ? 'selected' : '' ?>>Z - A</option>
                                            <option value="newest" <?= (($_GET['filterInactiveUsers'] ?? '') === 'newest') ? 'selected' : '' ?>>Newest</option>
                                            <option value="oldest" <?= (($_GET['filterInactiveUsers'] ?? '') === 'oldest') ? 'selected' : '' ?>>Oldest</option>
                                        </select>
                                    </div>

                                    <button type="submit" hidden></button>
                                </form>
                            </header>

                            <?php if ($inactiveUsersResultSet->num_rows > 0): ?>
                                <table class="table table-borderless">
                                    <thead>
                                        <th>ID</th>
                                        <th>Status</th>
                                        <th>Fullname</th>
                                        <th>Email</th>
                                        <th>Action</th>
                                    </thead>
                                    
                                    <tbody>
                                        <?php while ($inactiveUsersRow = $inactiveUsersResultSet->fetch_assoc()): ?>
                                            <tr>
                                            <td data-label="ID"><?= $inactiveUsersRow['id']; ?></td>
                                            <td data-label="Status"><?= $inactiveUsersRow['status']; ?></td>
                                            <td data-label="Full Name"><?= $inactiveUsersRow['fullname']; ?></td>
                                            <td data-label="Email"><?= $inactiveUsersRow['email']; ?></td>

                                            <td data-label="Action">
                                                <div class="action-buttons">
                                                    <button class="sm-btn primary-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#viewInactiveUserModal"
                                                        onclick="viewInactiveUserDetails(
                                                            '<?= htmlspecialchars($inactiveUsersRow['id']) ?>',
                                                            '<?= htmlspecialchars($inactiveUsersRow['fullname']) ?>',
                                                            '<?= htmlspecialchars($inactiveUsersRow['sex'] ?? '') ?>',
                                                            '<?= htmlspecialchars($inactiveUsersRow['dob'] ?? '') ?>',
                                                            '<?= htmlspecialchars($inactiveUsersRow['institute'] ?? '') ?>',
                                                            '<?= htmlspecialchars($inactiveUsersRow['program'] ?? '') ?>',
                                                            '<?= htmlspecialchars($inactiveUsersRow['status'] ?? '') ?>',
                                                            '<?= htmlspecialchars($inactiveUsersRow['email']) ?>',
                                                            '<?= htmlspecialchars($inactiveUsersRow['created_at']) ?>'
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
                                    <li class="page-item <?= ($inactiveUsersPage <= 1) ? 'disabled' : '' ?>">
                                        <?php if ($inactiveUsersPage > 1): ?>
                                            <a class="page-link"
                                                href="?tab=inactiveUsersTab&inactiveUsersPage=<?= $inactiveUsersPage - 1 ?>&searchInactiveUsers=<?= urlencode($_GET['searchInactiveUsers'] ?? '') ?>&filterInactiveUsers=<?= urlencode($_GET['filterInactiveUsers'] ?? '') ?>"
                                                Previous
                                            </a>
                                        <?php else: ?>
                                            <span class="page-link">Previous</span>
                                        <?php endif; ?>
                                    </li>

                                    <li class="page-item active">
                                        <span class="page-link"><?= $inactiveUsersPage ?></span>
                                    </li>

                                    <li class="page-item <?= ($inactiveUsersPage >= $totalInactiveUsersPages) ? 'disabled' : '' ?>">
                                        <?php if ($inactiveUsersPage < $totalInactiveUsersPages): ?>
                                            <a class="page-link"
                                                href="?tab=inactiveUsersTab&inactiveUsersPage=<?= $inactiveUsersPage - 1 ?>&searchInactiveUsers=<?= urlencode($_GET['searchInactiveUsers'] ?? '') ?>&filterInactiveUsers=<?= urlencode($_GET['filterInactiveUsers'] ?? '') ?>"
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
            </div>
        </div>

        <!-- View active user details modal -->
        <div class="modal fade" id="viewActiveUserModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="profile">
                            <i class="fa-solid fa-circle-user"></i>
                            <h4><span id="a_fullname"></span></h4>
                            <small><span id="a_username"></span> | <span id="a_id">@</span></small>
                        </div>
                        
                        <hr>
                        
                        <div class="profile-section">
                            <h6>Personal Information</h6>
                            <p>
                                <small><b>Sex: </b><span id="a_sex"></span></small>
                                <small><b>Date of Birth: </b><span id="a_dob"></span></small>
                            </p>
                        </div>

                        <hr>

                        <div class="profile-section">
                            <h6>Academic Information</h6>
                            <p>
                                <small><b>Institute: </b><span id="a_institute"></span></small>
                            </p>
                            <p>
                                <small><b>Program: </b><span id="a_program"></span></small>
                            </p>
                        </div>

                        <hr>

                        <div class="profile-section">
                            <h6>Account Information</h6>
                            <p>
                                <small><b>Email: </b><span id="a_email"></span></small>
                            </p>
                            <p>
                                <small><b>Status: </b><span id="a_status"></span></small>
                            </p>
                            <p>
                                <small><b>Date Activated: </b><span id="a_updated_at"></span></small>
                            </p>
                            <p>
                                <small><b>Date Created: </b><span id="a_created_at"></span></small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- View inactive user details modal -->
        <div class="modal fade" id="viewInactiveUserModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="profile">
                            <i class="fa-solid fa-circle-user"></i>
                            <h4><span id="i_fullname"></span></h4>
                            <small><span id="i_username"></span> | <span id="i_id">@</span></small>
                        </div>

                        <hr>

                        <div class="profile-section">
                            <h6>Personal Information</h6>
                            <p>
                                <small><b>Sex: </b><span id="i_sex"></span></small>
                                <small><b>Date of Birth: </b><span id="i_dob"></span></small>
                            </p>
                        </div>

                        <hr>

                        <div class="profile-section">
                            <h6>Academic Information</h6>
                            <p>
                                <small><b>Institute: </b><span id="i_institute"></span></small>
                            </p>
                            <p>
                                <small><b>Program: </b><span id="i_program"></span></small>
                            </p>
                        </div>

                        <hr>

                        <div class="profile-section">
                            <h6>Account Information</h6>
                            <p>
                                <small><b>Email: </b><span id="i_email"></span></small>
                            </p>
                            <p>
                                <small><b>Status: </b><span id="i_status"></span></small>
                            </p>
                            <p>
                                <small><b>Date Activated: </b><span id="i_updated_at"></span></small>
                            </p>
                            <p>
                                <small><b>Date Created: </b><span id="i_created_at"></span></small>
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
