<?php
session_start();
require '../app/db_connection.php';

if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] != 1) {
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

$usertype = $_SESSION['usertype'] ?? '';
$institute = $_SESSION['institute'] ?? '';
$program = $_SESSION['program'] ?? '';

$username = $_SESSION['username'] ?? '';
$email = $_SESSION['email'] ?? '';
$status = $_SESSION['status'] ?? '';

// Add user
$institute = $conn->query("SELECT id, description FROM institute");
$program = $conn->query("SELECT id, description FROM program");

// Update user
$instituteUpdate = $conn->query("SELECT id, description FROM institute");
$programUpdate = $conn->query("SELECT id, description FROM program");
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
                        <a class="link danger-btn" href="../app/logout.php">
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
                    <a class="link danger-btn" href="../app/logout.php">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        logout
                    </a>
                </li>
            </div>
        </ul>
    </nav>

    <section id="section">
        <header>
            <div class="menuToggleButtonContainer">
                <i class="fa-solid fa-bars menuToggleButton" data-bs-toggle="offcanvas" data-bs-target="#sidebarMobile"></i>
            </div>

            <h1 class="page-title">Users</h1>
        </header>

        <div class="content">
            <div id="users">
                <!-- Add user button -->
                <button class="btn add-user-btn" data-bs-toggle="modal" data-bs-target="#addUserModal"><i class="fa-solid fa-user-plus"></i></button>

                <!-- Users table -->
                <div class="table-container">
                    <div class="table-header">
                        <div class="search-group">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <form action="" method="GET">
                                <input type="search" name="searchUser" id="searchUser" placeholder="Search users..." value="<?= htmlspecialchars($_GET['searchUser'] ?? '') ?>">
                            </form>
                        </div>

                        <div class="filter-group">
                            <div class="filter-group">
                                <i class="fa-solid fa-filter"></i>

                                <form method="GET" action="users.php">

                                    <!-- keep search value -->
                                    <input type="hidden" name="searchUser" value="<?= htmlspecialchars($_GET['searchUser'] ?? '') ?>">

                                    <select name="status" onchange="this.form.submit()">
                                        <option value="">All Users</option>

                                        <option value="Active" <?= (($_GET['status'] ?? '') == 'Active') ? 'selected' : '' ?>>
                                            Active
                                        </option>

                                        <option value="Inactive" <?= (($_GET['status'] ?? '') == 'Inactive') ? 'selected' : '' ?>>
                                            Inactive
                                        </option>
                                    </select>

                                </form>
                            </div>
                        </div>
                    </div>

                    <table class="table table-borderless">
                        <thead>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Fullname</th>
                            <th>Email</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            <?php
                                $search_term = $_GET['searchUser'] ?? '';

                                if (!empty($search_term)) {
                                    $stmt = $conn->prepare("SELECT * FROM view_users WHERE fullname LIKE ? OR email LIKE ?");
                                    $like = "%" . $search_term . "%";
                                    $stmt->bind_param("ss", $like, $like);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                } else {
                                    require __DIR__ . '/../app/search_filter_users.php';
                                }
                            ?>

                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td data-label="ID"><?= $row['id']; ?></td>
                                    <td data-label="Username"><?= $row['username']; ?></td>
                                    <td data-label="Fullname"><?= $row['fullname']; ?></td>
                                    <td data-label="Email"><?= $row['email']; ?></td>
                                    <td data-label="Action">
                                        <div class="action-buttons">
                                            <button type="button" class="sm-btn primary-btn" data-bs-toggle="modal" data-bs-target="#viewUserModal" 
                                                onclick="viewUserDetails(
                                                    '<?= $row['id'] ?>', 
                                                    '<?= $row['fullname'] ?>', 
                                                    '<?= $row['sex'] ?>', 
                                                    '<?= $row['dob'] ?>', 
                                                    '<?= $row['institute'] ?>', 
                                                    '<?= $row['program'] ?>', 
                                                    '<?= $row['username'] ?>', 
                                                    '<?= $row['email'] ?>',
                                                    '<?= $row['status'] ?>'
                                                )">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>

                                            <button 
                                                type="button"
                                                class="sm-btn secondary-btn <?= $row['status'] === 'Active' ? 'disabled-btn' : '' ?>"
                                                data-bs-toggle="modal" 
                                                data-bs-target="<?= $row['status'] === 'Active' ? '' : '#updateUserModal' ?>"
                                                onclick="<?= $row['status'] === 'Active' ? 'return false;' : "updateUserDetails(
                                                    '{$row['id']}',
                                                    '{$row['firstname']}',
                                                    '{$row['middlename']}',
                                                    '{$row['lastname']}',
                                                    '{$row['sex']}',
                                                    '{$row['dob']}',
                                                    '{$row['institute_id']}',
                                                    '{$row['program_id']}',
                                                    '{$row['email']}'
                                                )" ?>"
                                            >
                                                <i class="fa-solid fa-user-pen"></i>
                                            </button>

                                            <button 
                                                type="button"
                                                class="sm-btn danger-btn <?= $row['status'] === 'Active' ? 'disabled-btn' : '' ?>"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteUserModal"
                                                onclick="<?= $row['status'] === 'Active' 
                                                    ? 'return false;' 
                                                    : "setDeleteUser('{$row['id']}')" ?>"
                                                <?= $row['status'] === 'Active' ? 'disabled' : '' ?>
                                            >
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add User Modal -->
        <div class="modal fade" id="addUserModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">
                            <i class="fa-solid fa-user"></i>
                            Add User
                        </h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <form method="POST" action="../app/add_user.php">
                            <div class="field-group">
                                <label>User Type</label>
                                <select name="userType" class="form-control" required>
                                    <option value="2" selected>User</option>
                                </select>
                            </div>
                    
                            <div class="field-group">
                                <label>Personal Information</label>
                                <input type="text" name="firstname" class="form-control" placeholder="First Name" required>
                                <input type="text" name="middlename" class="form-control" placeholder="Middle Name">
                                <input type="text" name="lastname" class="form-control" placeholder="Last Name" required>

                                <select name="sex" class="form-control" required>
                                    <option value="">Select Sex</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                                <input type="date" name="dob" class="form-control" required>
                            </div>

                            <div class="field-group">
                                <label>Academic Information</label>
                                <select name="institute" class="form-control">
                                    <option value="">Select Institute</option>
                                    <?php while ($row = $institute->fetch_assoc()) { ?>
                                        <option value="<?= $row['id'] ?>">
                                            <?= $row['description'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                
                                <select name="program" class="form-control">
                                    <option value="">Select Program</option>
                                    <?php while ($row = $program->fetch_assoc()) { ?>
                                        <option value="<?= $row['id'] ?>">
                                            <?= $row['description'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="field-group">
                                <label>Account Information</label>
                                <input type="email" name="email" class="form-control" placeholder="Email" required>
                            </div>

                            <div class="action-buttons">
                                <button type="submit" class="btn primary-btn">
                                    Save
                                </button>
                                <button type="button" class="btn secondary-btn" data-bs-dismiss="modal">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update user details modal -->
        <div class="modal fade" id="updateUserModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">
                            <i class="fa-solid fa-user-pen"></i>
                            Update User
                        </h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <form method="POST" action="../app/update_user.php">

                            <!-- Hidden ID -->
                            <input type="hidden" name="id" id="uu_id">

                            <div class="field-group">
                                <label>Personal Information</label>

                                <input type="text" name="firstname" id="uu_firstname" class="form-control" required>
                                <input type="text" name="middlename" id="uu_middlename" class="form-control">
                                <input type="text" name="lastname" id="uu_lastname" class="form-control" required>

                                <select name="sex" id="uu_sex" class="form-control">
                                    <option value="">Select Sex</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>

                                <input type="date" name="dob" id="uu_dob" class="form-control">
                            </div>

                            <div class="field-group">
                                <label>Academic Information</label>

                                <select name="institute" id="uu_institute" class="form-control">
                                    <option value="">Select Institute</option>
                                    <?php while ($row = $instituteUpdate->fetch_assoc()) { ?>
                                        <option value="<?= $row['id'] ?>">
                                            <?= $row['description'] ?>
                                        </option>
                                    <?php } ?>
                                </select>

                                <select name="program" id="uu_program" class="form-control">
                                    <option value="">Select Program</option>
                                    <?php while ($row = $programUpdate->fetch_assoc()) { ?>
                                        <option value="<?= $row['id'] ?>">
                                            <?= $row['description'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="field-group">
                                <label>Account Information</label>
                                <input type="email" name="email" id="uu_email" class="form-control" required>
                            </div>

                            <div class="action-buttons">
                                <button type="submit" class="btn primary-btn">
                                    Update
                                </button>
                                <button type="button" class="btn secondary-btn" data-bs-dismiss="modal">
                                    Cancel
                                </button>
                            </div>

                        </form>
                    </div>
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
                        <div class="profile-container">
                            <div class="profile">
                                <div class="profile-icon">
                                    <i class="fa-solid fa-circle-user"></i>
                                    <span class="badge rounded-pill" id="vu_userrole"></span>
                                </div>
                                <h5><span id="vu_fullname"></span></h5>
                            </div>
                            
                            
                            <div class="profile-section">
                                <h6>Personal Information</h6>
                                <p>
                                    <small><b>User ID: </b><span id="vu_id"></span></small>
                                    <small><b>Username: </b><span id="vu_username"></span></small>
                                </p>
                                <p>
                                    <small><b>Sex: </b><span id="vu_sex"></span></small>
                                    <small><b>Data of Birth: </b><span id="vu_dob"></span></small>
                                </p>
                            </div>

                            <div class="profile-section">
                                <h6>Academic Information</h6>
                                <p>
                                    <small><b>Institute: </b><span id="vu_institute"></span></small>
                                    <small><b>Program: </b><span id="vu_program"></span></small>
                                </p>
                            </div>

                            <div class="profile-section">
                                <h6>Account Information</h6>
                                <p>
                                    <small><b>Email: </b><span id="vu_email"></span></small>
                                    <small><b>Status: </b><span id="vu_status"></span></small>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete user modal -->
        <div class="modal fade confirmation-modal" id="deleteUserModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title">
                            <i class="fa-solid fa-trash"></i>
                            Delete User
                        </h4>
                    </div>

                    <div class="modal-body">
                        <form method="POST" action="../app/delete_user.php">
                            <input type="hidden" name="id" id="du_id">

                            <p>Are you sure you want to delete this user?</p>

                            <div class="action-buttons">
                                <button type="submit" class="btn primary-btn">
                                    Yes, Delete
                                </button>
                                <button type="button" class="btn secondary-btn" data-bs-dismiss="modal">
                                    No
                                </button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section>
</body>

<!-- Js -->
<script src="../assets/js/script.js"></script>
