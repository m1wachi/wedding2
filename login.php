<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f0f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 350px;
            display: flex;
            flex-direction: column;
            align-items: center; /* Center all children horizontally */
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        input {
            width: 100%; /* Keep full width */
            max-width: 300px; /* Set a maximum width for centering */
            padding: 10px;
            margin: 10px 10px; /* Consistent vertical margin */
            border: 1px solid #ccc;
            border-radius: 4px;
            text-align: center; /* Center text inside the input */
        }
        button {
            width: 100%; /* Make button full width */
            padding: 15px;
            background-color: #E75480;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            font-size: 16px;
            margin-top: 10px;
        }
        button:hover {
            background-color: #e91e63;
        }
        .link {
            text-align: center;
            margin-top: 15px;
        }
        .link a {
            color: #e91e63;
            text-decoration: underline;
            transition: color 0.3s; /* Smooth color transition */
        }
        .link a:hover {
            color: black; /* Change color to a more visible shade on hover */
            /* font-weight: bold;  */
        }
        .error {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
        .top-right-button {
            position: absolute;
            top: 30px;
            right: 30px;
        }
        .btn {
            padding: 10px 20px;
            background-color: #d81b60;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .btn:hover {
            background-color: #e91e63;
        }
    </style>
</head>
<body>
    <div class="top-right-button">
        <a href="index.html" class="btn">Go to Main Page</a>
    </div>

    <div class="container">
        <h2>Login</h2>
        <?php
        session_start();
        if (isset($_SESSION['error'])): ?>
            <div class="error">
                <?= htmlspecialchars($_SESSION['error']); ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <form method="POST" action="user.php">
            <input type="email" name="login_email" placeholder="Email" required>
            <input type="password" name="login_password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>
        
        <div class="link">
            <a href="register.php">Don't have an account? Register here</a>
        </div>
    </div>
</body>
</html>
