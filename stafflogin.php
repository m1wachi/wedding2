<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f0f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            position: relative; /* Added to position the button */
        }
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 350px;
            display: flex;
            flex-direction: column;
            align-items: center; /* Center all children */
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        input {
            width: 100%; /* Full width */
            max-width: 300px; /* Maximum width for centering */
            padding: 10px;
            margin: 10px 10px; /* Consistent vertical margin */
            border: 1px solid #ccc;
            border-radius: 4px;
            text-align: center;
        }
        button {
            width: 100%; /* Full width */
            padding: 15px; /* Increased padding for better click area */
            background-color: #E75480;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            font-size: 16px; /* Increased font size for readability */
            margin-top: 10px; /* Space above button */
        }
        button:hover {
            background-color: #e91e63;
        }
        .error {
            color: #d9534f;
            font-size: 0.9em;
            margin: 10px 0;
            text-align: center;
        }
        .top-right-button {
            position: absolute; /* Changed to absolute */
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
        <h2>Staff Login</h2>
        <?php
        session_start();
        if (isset($_SESSION['error'])): ?>
            <div class="error"><?= htmlspecialchars($_SESSION['error']); ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <form method="POST" action="stafflogin.php">
            <input type="email" name="login_email" placeholder="Email" required>
            <input type="password" name="login_password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>

        <?php
        // Database connection
        $host = 'localhost';
        $port = 3307;
        $dbName = 'user_auth';
        $user = 'root';
        $password = '';

        $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8";

        try {
            $pdo = new PDO($dsn, $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }

        // Handle staff login
        if (isset($_POST['login'])) {
            $login_email = $_POST['login_email'];
            $login_password = $_POST['login_password'];

            try {
                // Query the staff table to find the user
                $sql = "SELECT * FROM staff WHERE email = :email";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['email' => $login_email]);

                $staff = $stmt->fetch(PDO::FETCH_ASSOC);

                // Check if staff exists and password matches
                if ($staff && password_verify($login_password, $staff['password'])) {
                    // Set session variables
                    $_SESSION['id'] = $staff['id'];
                    $_SESSION['email'] = $staff['email'];
                    $_SESSION['role'] = $staff['role'];
                    session_regenerate_id(true); // Regenerate session ID for security

                    // Redirect based on role
                    if ($staff['role'] === 'admin') {
                        header("Location: admin.php"); // Redirect to admin dashboard
                        exit();
                    } elseif ($staff['role'] === 'bride' || $staff['role'] === 'groom') {
                        header("Location: Bride-Groom.php"); // Redirect to bride/groom page
                        exit();
                    } else {
                        $_SESSION['error'] = 'Unauthorized role.';
                        header("Location: stafflogin.php");
                        exit();
                    }
                } else {
                    $_SESSION['error'] = 'Invalid email or password.';
                    header("Location: stafflogin.php");
                    exit();
                }
            } catch (PDOException $e) {
                $_SESSION['error'] = "Error: " . $e->getMessage();
                header("Location: stafflogin.php");
                exit();
            }
        }
        ?>
    </div>
</body>
</html>
