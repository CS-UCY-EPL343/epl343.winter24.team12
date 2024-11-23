<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                2020 - <?php echo date('Y'); ?> &copy; 
                <?php
                // Dynamically set the system name based on the role
                if (isset($_SESSION['role'])) {
                    switch ($_SESSION['role']) {
                        case 1: // Admin
                            echo "Hospital Management System";
                            break;
                        case 2: // Doctor
                            echo "Hospital Management Information System";
                            break;
                        case 3: // Nurse
                            echo "Nursing Management Information System";
                            break;
                        case 4: // Secretary
                            echo "Secretary Management Information System";
                            break;
                        default:
                            echo "Hospital System";
                    }
                } else {
                    echo "Hospital System";
                }
                ?>
            </div>
        </div>
    </div>
</footer>
