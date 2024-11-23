<head>
    <meta charset="utf-8" />
    <title>
        <?php 
        // Dynamically set the page title based on user role
        if (isset($_SESSION['role'])) {
            switch ($_SESSION['role']) {
                case 1: echo "Admin Dashboard - HIMAROS"; break; // Admin
                case 2: echo "Doctor Dashboard - HIMAROS"; break; // Doctor
                case 3: echo "Nurse Dashboard - HIMAROS"; break; // Nurse
                case 4: echo "Secretary Dashboard - HIMAROS"; break; // Secretary
                default: echo "HIMAROS - Hospital Management System";
            }
        } else {
            echo "HIMAROS - Hospital Management System";
        }
        ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured hospital management system for efficient operations." name="description" />
    <meta content="HIMAROS Developers" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="../assets/images/favicon.ico">

    <!-- Plugins CSS -->
    <link href="../assets/libs/flatpickr/flatpickr.min.css" rel="stylesheet" type="text/css" />

    <!-- App CSS -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/app.min.css" rel="stylesheet" type="text/css" />

    <!-- Loading button CSS -->
    <link href="../assets/libs/ladda/ladda-themeless.min.css" rel="stylesheet" type="text/css" />

    <!-- Footable CSS -->
    <link href="../assets/libs/footable/footable.core.min.css" rel="stylesheet" type="text/css" />

    <!-- Sweet Alert JS -->
    <script src="../assets/js/swal.js"></script>

    <!-- Inject SweetAlert for success messages -->
    <?php if (isset($success)) { ?>
        <script>
            setTimeout(function () { 
                swal("Success", "<?php echo $success; ?>", "success");
            }, 100);
        </script>
    <?php } ?>

    <!-- Inject SweetAlert for error messages -->
    <?php if (isset($err)) { ?>
        <script>
            setTimeout(function () { 
                swal("Error", "<?php echo $err; ?>", "error");
            }, 100);
        </script>
    <?php } ?>

</head>
