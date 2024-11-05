<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.html");
    exit();
}

// Database connection
$host = 'localhost';
$port = 3307;
$dbName = 'user_auth';
$dbUser = 'root';
$dbPass = '';

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbName;charset=utf8";
    $pdo = new PDO($dsn, $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch the user information from the database
    $sql = "SELECT * FROM user_auth WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $_SESSION['id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch the RSVP information from the guest table
    $sql = "SELECT * FROM guest WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $_SESSION['email']]);
    $rsvp = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database query failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile</title>
    <style>
        body {
            font-family: 'Georgia', serif;
            margin: 0;
            padding: 0;
            background-color: #f8f0f6;
            color: #333;
            text-align: center;
        }
        .navbar {
            background-color: #f1c6d3;
            padding: 10px;
            color: #fff;
            position: relative;
        }
        .navbar a {
            color: #e91e63;
            text-decoration: none;
            padding: 10px 20px;
            font-size: 1em;
            position: absolute;
            top: 10px;
            right: 20px;
            border-radius: 4px;
            background-color: #fff;
        }
        .navbar a:hover {
            background-color: #e91e63;
            color: #fff;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #e91e63;
            font-size: 2em;
            margin-bottom: 20px;
            text-align: center;
            /* color: #333; */
        }
        form {
            text-align: left;
            margin: 0 auto;
            width: 100%;
            max-width: 500px;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            padding: 10px 20px;
            background-color: #e91e63;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1.2em;
        }
        button:hover {
            background-color: #d81b60;
            background-color: #f1c6d3;
        }
        .success {
            color: green;
            margin-top: 10px;
        }
        .error {
            color: red;
            margin-top: 10px;
        }
        .form-group {
        margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        select,
        input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color:#e91e63;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.6s;
        }
        #error-message {
            margin-top: 10px;
            text-align: center;
        }
    </style>
    
</head>
<body>
    <!-- Navigation Bar -->
    <div class="navbar">
        <a href="index.html">Back to Main Page</a>
    </div>

    <!-- Profile Information -->
    <div class="container">
        <h1>Your Profile</h1>
        <?php if (isset($_SESSION['update_success'])): ?>
            <div class="success"><?= htmlspecialchars($_SESSION['update_success']); ?></div>
            <?php unset($_SESSION['update_success']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['update_error'])): ?>
            <div class="error"><?= htmlspecialchars($_SESSION['update_error']); ?></div>
            <?php unset($_SESSION['update_error']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['rsvp_success'])): ?>
            <div class="success"><?= htmlspecialchars($_SESSION['rsvp_success']); ?></div>
            <?php unset($_SESSION['rsvp_success']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['rsvp_error'])): ?>
            <div class="error"><?= htmlspecialchars($_SESSION['rsvp_error']); ?></div>
            <?php unset($_SESSION['rsvp_error']); ?>
        <?php endif; ?>
        <form action="update_profile.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="New Password">

            <button type="submit">Update Profile</button>
        </form>
    </div>


    <div class="container">
    <h1>Your RSVP Information</h1>
    <form id="rsvpForm" action="update_rsvp.php" method="POST">
        <div class="form-group">
            <label for="rsvp_status">Will you attend the wedding?</label>
            <select id="rsvp_status" name="rsvp_status" required>
                <option value="Yes" <?= $rsvp['rsvp_status'] == 'Yes' ? 'selected' : '' ?>>Yes</option>
                <option value="No" <?= $rsvp['rsvp_status'] == 'No' ? 'selected' : '' ?>>No</option>
                <option value="Maybe" <?= $rsvp['rsvp_status'] == 'Maybe' ? 'selected' : '' ?>>Maybe</option>
            </select>
        </div>

        <div class="form-group">
            <label for="num_guests">Number of Guests Attending:</label>
            <input type="number" id="num_guests" name="num_guests" value="<?= htmlspecialchars($rsvp['no_of_guest']) ?>" min="0" <?= $rsvp['rsvp_status'] == 'No' ? 'disabled' : '' ?>>
        </div>

        <button type="submit">Update RSVP</button>
        <p id="error-message" style="color:red; display:none;">Number of guests cannot be 0 when attending!</p>
    </form>
</div>

<script>
    document.getElementById('rsvp_status').addEventListener('change', function() {
        var rsvpStatus = this.value;
        var numGuests = document.getElementById('num_guests');
        
        if (rsvpStatus === 'Yes' || rsvpStatus === 'Maybe') {
            if (numGuests.value == 0) {
                numGuests.value = 1; 
            }
            numGuests.disabled = false; 
        } else {
            numGuests.value = 0;
            numGuests.disabled = true; 
        }
    });

    document.getElementById('rsvpForm').addEventListener('submit', function(event) {
        var rsvpStatus = document.getElementById('rsvp_status').value;
        var numGuests = document.getElementById('num_guests').value;
        var errorMessage = document.getElementById('error-message');

        if ((rsvpStatus === 'Yes' || rsvpStatus === 'Maybe') && numGuests == 0) {
            event.preventDefault(); 
            errorMessage.style.display = 'block'; 
        } else {
            errorMessage.style.display = 'none'; 
        }
    });
</script>

</body>
</html>
