<!DOCTYPE html> 
<html lang="en">
<head>
    <!-- Required Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>HIMAROS - Hospital Management System</title>
    <link rel="stylesheet" href="assets/css/bootstrap-4.1.3.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="header-area">
        <div id="header" id="home">
            <div class="container">
                <div class="row align-items-center justify-content-between d-flex">
                    <div id="logo">
                        <a href="index.php">
                            <img src="assets/images/logo-dark.png" alt="HIMAROS">
                        </a>
                    </div>
                    <nav id="nav-menu-container">
                        <ul class="nav-menu">
                            <li class="menu-active"><a href="index.php">Home</a></li>
                            <li><a href="backend/login.php">Login</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <section class="banner-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <h1>HIMAROS</h1>
                    <p>Hospital Inventory Management And Resource Optimization Software</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <p>2020 - <?php echo date('Y'); ?> &copy; HIMAROS - Hospital Management System.</p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
