<?php
session_start();
include(__DIR__ . '/../../config/database.php'); 
include('../assets/inc/checklogin.php');
checklogin('admin'); // Role = 'admin'

$user_id = $_SESSION['user_id'];
$mysqli = Database::getConnection();
?>
<!DOCTYPE html>
<html lang="en">
<?php include('../assets/inc/head.php'); ?>
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
                                <h4 class="page-title">Admin Dashboard</h4>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-xl-4">
                            <div class="widget-rounded-circle card-box">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="avatar-lg rounded-circle bg-soft-primary border-primary border">
                                            <i class="fas fa-user-md font-22 avatar-title text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-right">
                                            <?php
                                            $stmt = $mysqli->prepare("SELECT COUNT(*) FROM USERS WHERE User_Role IN ('doctor', 'nurse', 'secretary')");
                                            $stmt->execute();
                                            $stmt->bind_result($employees);
                                            $stmt->fetch();
                                            $stmt->close();
                                            ?>
                                            <h3 class="text-dark mt-1"><?php echo $employees; ?></h3>
                                            <p class="text-muted mb-1">Employees</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card-box">
                                <h4 class="header-title mb-3">Recent Employees</h4>
                                <div class="table-responsive">
                                    <table class="table table-borderless table-hover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $stmt = $mysqli->prepare("SELECT * FROM USERS WHERE User_Role IN ('doctor', 'nurse', 'secretary') ORDER BY Created_At DESC LIMIT 10");
                                            $stmt->execute();
                                            $res = $stmt->get_result();
                                            while ($row = $res->fetch_object()) {
                                                ?>
                                                <tr>
                                                    <td><?php echo $row->First_Name . ' ' . $row->Last_Name; ?></td>
                                                    <td><?php echo $row->Email; ?></td>
                                                    <td><?php echo ucfirst($row->User_Role); ?></td>
                                                    <td>
                                                        <a href="admin_view_user.php?UserID=<?php echo $row->UserID; ?>" class="btn btn-sm btn-primary">View</a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include('../assets/inc/footer.php'); ?>
        </div>
    </div>
</body>
</html>
