<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HIMAROS - Hospital Management System</title>
    <style>
        /* Global Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: url('logos/long-white.png') no-repeat center center fixed; /* Set background image */
            background-size: cover; /* Cover the entire viewport */
            background-size: 100% 50%; /* Set custom width and height for the image */
            background-position: center; /* Center the image */
            color: #333; /* Dark grey text color for better readability */
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Header Styling */
        .header-area {
            padding: 15px 30px;
            display: flex;
            justify-content: flex-end; /* Align navigation to the right */
            align-items: center;
        }

        .nav-menu {
            list-style: none;
            display: flex;
            gap: 30px;
        }

        .nav-menu a {
            text-decoration: none;
            color: #333; /* Dark grey color for links */
            font-weight: bold;
        }

        .nav-menu a:hover {
            color: #1a4f6e; /* Darker blue hover effect */
        }

        /* Footer Styling */
        .footer-area {
            padding: 20px;
            text-align: center;
            margin-top: auto;
            color: #333; /* Dark grey text color */
        }

        .footer-area p {
            margin: 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header-area">
        <nav>
            <ul class="nav-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="backend/login.php">Login</a></li>
            </ul>
        </nav>
    </header>

    <!-- Footer -->
    <footer class="footer-area">
        <p>&copy; HIMAROS - Hospital Management System.</p>
    </footer>
</body>
</html>
