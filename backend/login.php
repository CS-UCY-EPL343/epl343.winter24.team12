<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            display: flex;
            height: 100vh;
            margin: 0;
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to right, #fff 50%, #216491 50%);
        }
        .left-section {
            width: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 0;
            overflow: hidden; /* Ensures no overflow outside the container */
        }
        .left-section img {
            max-width: 80%; /* Slightly reduced width */
            max-height: 80%; /* Maintains proportions */
            object-fit: contain; /* Keeps the image within boundaries without cropping */
        }
        .right-section {
            width: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        .card {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            padding: 40px 30px;
            width: 350px;
            text-align: center;
        }
        .card h3 {
            font-weight: bold;
            color: #333;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            text-align: left;
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
        }
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ccc;
            border-radius: 20px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .form-control:focus {
            border-color: #216491;
            box-shadow: 0 0 5px rgba(33, 100, 145, 0.5);
            outline: none;
        }
        .btn-primary {
            display: block;
            width: 100%;
            background-color: #4c8cb4; /* Lighter version of #216491 */
            border: none;
            padding: 12px;
            font-size: 16px;
            color: white;
            border-radius: 20px;
            cursor: pointer;
            margin-top: 10px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-primary:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>
    <div class="left-section">
        <img src="../assets/images/logo.jpg" alt="HIMAROS Logo">
    </div>
    <div class="right-section">
        <div class="card">
            <h3>Welcome to HIMAROS!</h3>
            <?php if (isset($err)) { ?>
                <div class="alert alert-danger text-center">
                    <?php echo htmlspecialchars($err); ?>
                </div>
            <?php } ?>
            <form method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <button type="submit" name="login" class="btn-primary">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
