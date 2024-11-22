<?php 
session_start();

// Database connection
include(__DIR__ . '/../../config/database.php'); 
include('../assets/inc/checklogin.php');
checklogin(3); // Role 3 = Nurse

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
                                <h4 class="page-title">Nurse Dashboard</h4>
                            </div>
                        </div>
                    </div>     

                    <div class="row">
                        <!-- Start Inventory -->
                        <div class="col-md-6 col-xl-4">
                            <div class="widget-rounded-circle card-box">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="avatar-lg rounded-circle bg-soft-primary border-primary border">
                                            <i class="mdi mdi-hospital-box-outline font-22 avatar-title text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-right">
                                            <?php
                                                $result = "SELECT COUNT(*) FROM CURRENT_STOCK WHERE Quantity > 0";
                                                $stmt = $mysqli->prepare($result);
                                                $stmt->execute();
                                                $stmt->bind_result($inventory);
                                                $stmt->fetch();
                                                $stmt->close();
                                            ?>
                                            <h3 class="text-dark mt-1"><span><?php echo $inventory; ?></span></h3>
                                            <p class="text-muted mb-1 text-truncate">Inventory Items</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Inventory -->

                        <!-- Start Item Usage -->
                        <div class="col-md-6 col-xl-4">
                            <div class="widget-rounded-circle card-box">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="avatar-lg rounded-circle bg-soft-primary border-primary border">
                                            <i class="mdi mdi-medical-bag font-22 avatar-title text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-right">
                                            <?php
                                                $result = "SELECT COUNT(*) FROM OPERATION_ITEM_USAGE";
                                                $stmt = $mysqli->prepare($result);
                                                $stmt->execute();
                                                $stmt->bind_result($usedItems);
                                                $stmt->fetch();
                                                $stmt->close();
                                            ?>
                                            <h3 class="text-dark mt-1"><span><?php echo $usedItems; ?></span></h3>
                                            <p class="text-muted mb-1 text-truncate">Items Used</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Item Usage -->
                    </div>
                </div>
            </div>
            <?php include('../assets/inc/footer.php'); ?>
        </div>
    </div>
</body>
</html>
