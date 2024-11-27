<?php
// Role-based Sidebar Items
$sidebar_items = [
    'admin' => [
        ['icon' => 'fe-airplay', 'label' => 'Dashboard', 'href' => 'admin_dashboard.php'],
        ['icon' => 'mdi mdi-doctor', 'label' => 'Employees', 'submenu' => [
            ['href' => 'admin_add_employee.php', 'label' => 'Add Employee'],
            ['href' => 'admin_view_employee.php', 'label' => 'View Employees'],
            ['href' => 'admin_manage_employee.php', 'label' => 'Manage Employees'],
        ]],
        ['icon' => 'mdi mdi-pill', 'label' => 'Pharmacy', 'submenu' => [
            ['href' => 'admin_add_pharmaceuticals.php', 'label' => 'Add Pharmaceuticals'],
            ['href' => 'admin_view_pharmaceuticals.php', 'label' => 'View Pharmaceuticals'],
        ]],
    ],
    'doctor' => [
        ['icon' => 'fe-airplay', 'label' => 'Dashboard', 'href' => 'doc_dashboard.php'],
        ['icon' => 'fe-hard-drive', 'label' => 'Reports', 'href' => 'doc_report.php'],
    ],
    'nurse' => [
        ['icon' => 'fe-airplay', 'label' => 'Dashboard', 'href' => 'nurse_dashboard.php'],
        ['icon' => 'fe-box', 'label' => 'Manage Inventory', 'href' => 'nurse_inventory.php'],
    ],
    'secretary' => [
        ['icon' => 'fe-airplay', 'label' => 'Dashboard', 'href' => 'secretary_dashboard.php'],
        ['icon' => 'fe-calendar', 'label' => 'Manage Schedule', 'href' => 'secretary_schedule.php'],
    ],
];

$role = $_SESSION['role'];
?>
<div class="left-side-menu">
    <div class="slimscroll-menu">
        <!-- Sidebar -->
        <div id="sidebar-menu">
            <ul class="metismenu" id="side-menu">
                <?php if (isset($sidebar_items[$role])): ?>
                    <?php foreach ($sidebar_items[$role] as $item): ?>
                        <li>
                            <a href="<?= $item['href'] ?? 'javascript:void(0);' ?>">
                                <i class="<?= $item['icon'] ?>"></i>
                                <span><?= $item['label'] ?></span>
                                <?php if (isset($item['submenu'])): ?>
                                    <span class="menu-arrow"></span>
                                <?php endif; ?>
                            </a>
                            <?php if (isset($item['submenu'])): ?>
                                <ul class="nav-second-level" aria-expanded="false">
                                    <?php foreach ($item['submenu'] as $submenu): ?>
                                        <li>
                                            <a href="<?= $submenu['href'] ?>"><?= $submenu['label'] ?></a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>
