<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
            padding: 30px; /* Adjusted padding for consistency */
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 350px; /* Width consistent with the login page */
            display: flex;
            flex-direction: column;
            align-items: center; /* Center all children */
        }
        h2 {
            text-align: center;
            margin-bottom: 20px; /* Spacing for the heading */
        }
        input {
            width: 100%; /* Full width */
            max-width: 300px; /* Maximum width for centering */
            padding: 10px;
            margin: 10px 10px; /* Consistent vertical margin */
            border: 1px solid #ccc;
            border-radius: 4px;
            text-align: center; /* Center text inside the input */
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
        .link {
            text-align: center;
            margin-top: 15px; /* Increased margin for spacing */
        }
        .link a {
            color: #e91e63;
            text-decoration: underline;
            transition: color 0.3s; /* Smooth color transition */
        }
        .link a:hover {
            color: black;
        }
        .error {
            color: #d9534f;
            font-size: 0.9em;
            margin: 10px 0;
            text-align: center; /* Center error messages */
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Register</h2>
    <form method="POST" action="user.php">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="register">Register</button>
    </form>
    <div class="link">
        <a href="login.php">Already have an account? Login here</a>
    </div>
    <?php
    if (isset($_GET['error']) && $_GET['error'] === 'email_exists') {
        echo "<p class='error'>This email is already registered. Please use a different email.</p>";
    }
    ?>
</div>
</body>
</html>
