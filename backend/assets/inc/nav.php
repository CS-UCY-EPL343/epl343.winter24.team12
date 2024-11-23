<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];
$mysqli = Database::getConnection();

$ret = "SELECT First_Name, Last_Name FROM USERS WHERE UserID = ? AND Role = ?";
$stmt = $mysqli->prepare($ret);
$stmt->bind_param('ii', $user_id, $role);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_object();

// Role-specific navigation items
$nav_items = [
    1 => [ // Admin
        ['href' => 'admin_add_employee.php', 'icon' => 'fe-users', 'label' => 'Employee'],
        ['href' => 'admin_report.php', 'icon' => 'fe-hard-drive', 'label' => 'Report'],
        ['href' => 'admin_surgery_records.php', 'icon' => 'fe-anchor', 'label' => 'Operation Report'],
    ],
    2 => [ // Doctor
        ['href' => 'doc_report.php', 'icon' => 'fe-hard-drive', 'label' => 'Report'],
    ],
    3 => [ // Nurse
        ['href' => 'nurse_inventory.php', 'icon' => 'fe-box', 'label' => 'Manage Inventory'],
        ['href' => 'nurse_reports.php', 'icon' => 'fe-file-text', 'label' => 'View Reports'],
    ],
    4 => [ // Secretary
        ['href' => 'secretary_schedule.php', 'icon' => 'fe-calendar', 'label' => 'Manage Schedule'],
        ['href' => 'secretary_alerts.php', 'icon' => 'fe-bell', 'label' => 'Manage Alerts'],
    ],
];
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
                <a href="<?php echo ($role == 1 ? 'his_admin_update_account.php' : ($role == 2 ? 'his_doc_update_account.php' : '#')); ?>" class="dropdown-item notify-item">
                    <i class="fe-user"></i>
                    <span>Update Account</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="<?php echo ($role == 1 ? 'his_admin_logout_partial.php' : ($role == 2 ? 'his_doc_logout_partial.php' : '#')); ?>" class="dropdown-item notify-item">
                    <i class="fe-log-out"></i>
                    <span>Logout</span>
                </a>
            </div>
        </li>
    </ul>

    <!-- LOGO -->
    <div class="logo-box">
        <a href="<?php echo ($role == 1 ? 'admin_dashboard.php' : ($role == 2 ? 'doctor_dashboard.php' : ($role == 3 ? 'nurse_dashboard.php' : 'secretary_dashboard.php'))); ?>" class="logo text-center">
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
        <li class="dropdown d-none d-lg-block">
            <a class="nav-link dropdown-toggle waves-effect waves-light" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                Create New
                <i class="mdi mdi-chevron-down"></i>
            </a>
            <div class="dropdown-menu">
                <?php foreach ($nav_items[$role] as $item): ?>
                    <a href="<?php echo $item['href']; ?>" class="dropdown-item">
                        <i class="<?php echo $item['icon']; ?> mr-1"></i>
                        <span><?php echo $item['label']; ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </li>
    </ul>
</div>
