<head>
    <meta charset="utf-8" />
    <title>
        <?php 
        if (isset($_SESSION['role'])) {
            echo ucfirst($_SESSION['role']) . " Dashboard - HIMAROS";
        } else {
            echo "HIMAROS - Hospital Management System";
        }
        ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured hospital management system for efficient operations." name="description" />
    <meta content="HIMAROS Developers" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- Favicon -->
    <link rel="shortcut icon" href="../assets/images/favicon.ico">

    <!-- Plugins CSS -->
    <link href="../assets/libs/flatpickr/flatpickr.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/libs/ladda/ladda-themeless.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/libs/footable/footable.core.min.css" rel="stylesheet" type="text/css" />

    <!-- Sweet Alert -->
    <script src="../assets/js/swal.js"></script>

    <?php if (isset($success)) { ?>
        <script>
            setTimeout(function () { 
                swal("Success", "<?php echo $success; ?>", "success");
            }, 100);
        </script>
    <?php } ?>

    <?php if (isset($err)) { ?>
        <script>
            setTimeout(function () { 
                swal("Error", "<?php echo $err; ?>", "error");
            }, 100);
        </script>
    <?php } ?>
</head>
