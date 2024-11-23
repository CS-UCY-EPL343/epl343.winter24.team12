<?php 
session_start();

// Database connection
include(__DIR__ . '/../../config/database.php'); 
include('../assets/inc/checklogin.php');
checklogin(4); // Role 4 = Secretary

$user_id = $_SESSION['user_id'];
$mysqli = Database::getConnection();

?>
<!DOCTYPE html>
<html lang="en">
    
<?php include("../assets/inc/head.php"); ?>

<body>
    <div id="wrapper">
        <?php include('../assets/inc/nav.php'); ?>
        <?php include('../assets/inc/sidebar.php'); ?>
        
        <div class="content-page">
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <h4 class="page-title">Secretary Dashboard</h4>
                            </div>
                        </div>
                    </div>     

                    <div class="row">
                        <!-- Start Suppliers -->
                        <div class="col-md-6 col-xl-4">
                            <div class="widget-rounded-circle card-box">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="avatar-lg rounded-circle bg-soft-success border-success border">
                                            <i class="mdi mdi-truck-delivery font-22 avatar-title text-success"></i>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-right">
                                            <?php
                                                $result = "SELECT COUNT(*) FROM SUPPLIER";
                                                $stmt = $mysqli->prepare($result);
                                                $stmt->execute();
                                                $stmt->bind_result($suppliers);
                                                $stmt->fetch();
                                                $stmt->close();
                                            ?>
                                            <h3 class="text-dark mt-1"><span><?php echo $suppliers; ?></span></h3>
                                            <p class="text-muted mb-1 text-truncate">Suppliers</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Suppliers -->

                        <!-- Start Operations -->
                        <div class="col-md-6 col-xl-4">
                            <div class="widget-rounded-circle card-box">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="avatar-lg rounded-circle bg-soft-success border-success border">
                                            <i class="mdi mdi-clipboard-check font-22 avatar-title text-success"></i>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-right">
                                            <?php
                                                $result = "SELECT COUNT(*) FROM OPERATION";
                                                $stmt = $mysqli->prepare($result);
                                                $stmt->execute();
                                                $stmt->bind_result($operations);
                                                $stmt->fetch();
                                                $stmt->close();
                                            ?>
                                            <h3 class="text-dark mt-1"><span><?php echo $operations; ?></span></h3>
                                            <p class="text-muted mb-1 text-truncate">Operations</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Operations -->
                    </div>
                </div>
            </div>
            <?php include('../assets/inc/footer.php'); ?>
        </div>
    </div>
</body>
</html>
