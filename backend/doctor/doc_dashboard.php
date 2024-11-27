<?php
session_start();

include(__DIR__ . '/../../config/database.php'); 
include('../assets/inc/checklogin.php');
checklogin('doctor'); // Role = 'doctor'

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
                                <h4 class="page-title">Doctor Dashboard</h4>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-xl-4">
                            <div class="widget-rounded-circle card-box">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="avatar-lg rounded-circle bg-soft-danger border-danger border">
                                            <i class="mdi mdi-surgical-mask font-22 avatar-title text-danger"></i>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-right">
                                            <?php
                                            $stmt = $mysqli->prepare("SELECT COUNT(*) FROM OPERATION");
                                            $stmt->execute();
                                            $stmt->bind_result($operationCount);
                                            $stmt->fetch();
                                            $stmt->close();
                                            ?>
                                            <h3 class="text-dark mt-1"><?php echo $operationCount; ?></h3>
                                            <p class="text-muted mb-1 text-truncate">Total Operations</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-4">
                            <div class="widget-rounded-circle card-box">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="avatar-lg rounded-circle bg-soft-success border-success border">
                                            <i class="mdi mdi-pill font-22 avatar-title text-success"></i>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-right">
                                            <?php
                                            $stmt = $mysqli->prepare("SELECT COUNT(*) FROM CURRENT_STOCK");
                                            $stmt->execute();
                                            $stmt->bind_result($stockCount);
                                            $stmt->fetch();
                                            $stmt->close();
                                            ?>
                                            <h3 class="text-dark mt-1"><?php echo $stockCount; ?></h3>
                                            <p class="text-muted mb-1 text-truncate">Stock Items</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card-box">
                                <h4 class="header-title mb-3">Recent Operations</h4>
                                <div class="table-responsive">
                                    <table class="table table-borderless table-hover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Operation Type</th>
                                                <th>Performed At</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $stmt = $mysqli->prepare("SELECT Operation_Type, Performed_At FROM OPERATION ORDER BY Performed_At DESC LIMIT 10");
                                            $stmt->execute();
                                            $stmt->bind_result($type, $performedAt);
                                            while ($stmt->fetch()) {
                                                echo "<tr>
                                                        <td>$type</td>
                                                        <td>$performedAt</td>
                                                    </tr>";
                                            }
                                            $stmt->close();
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include('../assets/inc/footer.php'); ?>
  
