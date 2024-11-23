<?php
// Role-based Sidebar Items
$sidebar_items = [
    1 => [ // Admin
        [
            'icon' => 'fe-airplay',
            'label' => 'Dashboard',
            'href' => 'admin_dashboard.php',
        ],
        [
            'icon' => 'mdi mdi-doctor',
            'label' => 'Employees',
            'submenu' => [
                ['href' => 'admin_add_employee.php', 'label' => 'Add Employee'],
                ['href' => 'admin_view_employee.php', 'label' => 'View Employees'],
                ['href' => 'admin_manage_employee.php', 'label' => 'Manage Employees'],
                ['divider' => true],
                ['href' => 'admin_assign_dept.php', 'label' => 'Assign Department'],
                ['href' => 'admin_transfer_employee.php', 'label' => 'Transfer Employee'],
            ],
        ],
        [
            'icon' => 'mdi mdi-pill',
            'label' => 'Pharmacy',
            'submenu' => [
                ['href' => 'admin_add_pharm_cat.php', 'label' => 'Add Pharm Category'],
                ['href' => 'admin_view_pharm_cat.php', 'label' => 'View Pharm Category'],
                ['href' => 'admin_manage_pharm_cat.php', 'label' => 'Manage Pharm Category'],
                ['divider' => true],
                ['href' => 'admin_add_pharmaceuticals.php', 'label' => 'Add Pharmaceuticals'],
                ['href' => 'admin_view_pharmaceuticals.php', 'label' => 'View Pharmaceuticals'],
                ['href' => 'admin_manage_pharmaceuticals.php', 'label' => 'Manage Pharmaceuticals'],
                ['divider' => true],
                ['href' => 'admin_add_presc.php', 'label' => 'Add Prescriptions'],
                ['href' => 'admin_view_presc.php', 'label' => 'View Prescriptions'],
                ['href' => 'admin_manage_presc.php', 'label' => 'Manage Prescriptions'],
            ],
        ],
        [
            'icon' => 'mdi mdi-cash-multiple',
            'label' => 'Accounting',
            'submenu' => [
                ['href' => 'admin_add_acc.payable.php', 'label' => 'Add Acc. Payable'],
                ['href' => 'admin_manage_acc_payable.php', 'label' => 'Manage Acc. Payable'],
                ['divider' => true],
                ['href' => 'admin_add_acc_receivable.php', 'label' => 'Add Acc. Receivable'],
                ['href' => 'admin_manage_acc_receivable.php', 'label' => 'Manage Acc. Receivable'],
            ],
        ],
        [
            'icon' => 'fas fa-funnel-dollar',
            'label' => 'Inventory',
            'submenu' => [
                ['href' => 'admin_pharm_inventory.php', 'label' => 'Pharmaceuticals'],
                ['href' => 'admin_equipments_inventory.php', 'label' => 'Assets'],
            ],
        ],
        [
            'icon' => 'fe-share',
            'label' => 'Reporting',
            'submenu' => [
                ['href' => 'admin_employee_records.php', 'label' => 'Employee Records'],
                ['href' => 'admin_pharmaceutical_records.php', 'label' => 'Pharmaceutical Records'],
                ['href' => 'admin_accounting_records.php', 'label' => 'Accounting Records'],
            ],
        ],
        [
            'icon' => 'mdi mdi-scissors-cutting',
            'label' => 'Surgical',
            'submenu' => [
                ['href' => 'admin_add_equipment.php', 'label' => 'Add Equipment'],
                ['href' => 'admin_manage_equipment.php', 'label' => 'Manage Equipments'],
                ['href' => 'admin_surgery_records.php', 'label' => 'Surgery Records'],
            ],
        ],
        [
            'icon' => 'fas fa-user-tag',
            'label' => 'Vendors',
            'submenu' => [
                ['href' => 'admin_add_vendor.php', 'label' => 'Add Vendor'],
                ['href' => 'admin_manage_vendor.php', 'label' => 'Manage Vendors'],
            ],
        ],
        [
            'icon' => 'fas fa-lock',
            'label' => 'Password Resets',
            'submenu' => [
                ['href' => 'admin_manage_password_resets.php', 'label' => 'Manage'],
            ],
        ],
    ],
    2 => [ // Doctor
        [
            'icon' => 'fe-airplay',
            'label' => 'Dashboard',
            'href' => 'doc_dashboard.php',
        ],
        [
            'icon' => 'fe-hard-drive',
            'label' => 'Reports',
            'href' => 'doc_report.php',
        ],
    ],
    3 => [ // Nurse
        [
            'icon' => 'fe-airplay',
            'label' => 'Dashboard',
            'href' => 'nurse_dashboard.php',
        ],
        [
            'icon' => 'fe-box',
            'label' => 'Manage Inventory',
            'href' => 'nurse_inventory.php',
        ],
        [
            'icon' => 'fe-file-text',
            'label' => 'View Reports',
            'href' => 'nurse_reports.php',
        ],
    ],
    4 => [ // Secretary
        [
            'icon' => 'fe-airplay',
            'label' => 'Dashboard',
            'href' => 'secretary_dashboard.php',
        ],
        [
            'icon' => 'fe-calendar',
            'label' => 'Manage Schedule',
            'href' => 'secretary_schedule.php',
        ],
        [
            'icon' => 'fe-bell',
            'label' => 'Manage Alerts',
            'href' => 'secretary_alerts.php',
        ],
    ],
];

// Get the current user's role from the session
$role = $_SESSION['role'];
?>
<div class="left-side-menu">
    <div class="slimscroll-menu">
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <ul class="metismenu" id="side-menu">
                <?php if (isset($sidebar_items[$role])): ?>
                    <?php foreach ($sidebar_items[$role] as $item): ?>
                        <li>
                            <a href="<?= $item['href'] ?? 'javascript: void(0);' ?>">
                                <i class="<?= $item['icon'] ?>"></i>
                                <span><?= $item['label'] ?></span>
                                <?php if (isset($item['submenu'])): ?>
                                    <span class="menu-arrow"></span>
                                <?php endif; ?>
                            </a>
                            <?php if (isset($item['submenu'])): ?>
                                <ul class="nav-second-level" aria-expanded="false">
                                    <?php foreach ($item['submenu'] as $submenu): ?>
                                        <?php if (isset($submenu['divider']) && $submenu['divider']): ?>
                                            <hr>
                                        <?php else: ?>
                                            <li>
                                                <a href="<?= $submenu['href'] ?>"><?= $submenu['label'] ?></a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
        <!-- End Sidebar -->
        <div class="clearfix"></div>
    </div>
</div>
