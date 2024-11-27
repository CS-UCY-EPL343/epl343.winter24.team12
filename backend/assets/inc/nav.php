<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];
$mysqli = Database::getConnection();

// Fetch user details
$ret = "SELECT First_Name, Last_Name FROM USERS WHERE UserID = ? AND User_Role = ?";
$stmt = $mysqli->prepare($ret);
$stmt->bind_param('is', $user_id, $role);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_object();
?>
<div class="navbar-custom">
    <ul class="list-unstyled topnav-menu float-right mb-0">
        <li class="d-none d-sm-block">
            <form class="app-search">
                <div class="app-search-box">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search...">
                        <div class="input-group-append">
                            <button class="btn" type="submit">
                                <i class="fe-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </li>

        <li class="dropdown notification-list">
            <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect waves-light" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                <img src="../../assets/images/users/default_user.png" alt="dpic" class="rounded-circle">
                <span class="pro-user-name ml-1">
                    <?php echo $user->First_Name . ' ' . $user->Last_Name; ?> <i class="mdi mdi-chevron-down"></i>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right profile-dropdown">
                <a href="update_account.php" class="dropdown-item notify-item">
                    <i class="fe-user"></i>
                    <span>Update Account</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="logout.php" class="dropdown-item notify-item">
                    <i class="fe-log-out"></i>
                    <span>Logout</span>
                </a>
            </div>
        </li>
    </ul>

    <!-- LOGO -->
    <div class="logo-box">
        <a href="<?php echo "{$role}_dashboard.php"; ?>" class="logo text-center">
            <span class="logo-lg">
                <img src="../../assets/images/logo-light.png" alt="" height="18">
            </span>
            <span class="logo-sm">
                <img src="../../assets/images/logo-sm-white.png" alt="" height="24">
            </span>
        </a>
    </div>

    <ul class="list-unstyled topnav-menu topnav-menu-left m-0">
        <li>
            <button class="button-menu-mobile waves-effect waves-light">
                <i class="fe-menu"></i>
            </button>
        </li>
    </ul>
</div>
