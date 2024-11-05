<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RSVP Submission</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
            text-align: center;
        }
        h1 {
            color: #e91e63;
        }
        p {
            font-size: 1.1em;
            margin: 20px 0;
        }
        .message {
            padding: 15px;
            border-radius: 5px;
            color: #fff;
            font-weight: bold;
        }
        .success {
            background-color: #4caf50;
        }
        .error {
            background-color: #f44336;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #e91e63;
            color: #fff;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 20px;
        }
        .button:hover {
            background-color: #d81b60;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        // Database connection
        $host = 'localhost';
        $port = 3307; // Default MySQL port is 3306
        $dbName = 'user_auth';
        $user = 'root';
        $password = ''; // If no password is set, leave this empty.

        $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8";

        try {
            $pdo = new PDO($dsn, $user, $password);
            // Set PDO to throw exceptions for errors
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }

        // Retrieve form data
        $fullname = strtoupper(trim($_POST['name']));
        $email = trim($_POST['email']);
        $rsvp_status = trim($_POST['attending']);
        $no_of_guests = isset($_POST['num_guests']) ? trim($_POST['num_guests']) : '';

        // Validate number of guests only if attending
        if (($rsvp_status === 'Yes' || $rsvp_status === 'Maybe') && intval($no_of_guests) < 1) {
            echo "<p class='message error'>Number of guests must be at least 1 if you are attending.</p>";
            exit();
        }

        try {
            // Check if the guest already exists
            $sql = "SELECT * FROM guest WHERE fullname = :fullname";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['fullname' => $fullname]);
            $guest = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($guest) {
                // Update existing guest record
                $sql = "UPDATE guest SET rsvp_status = :rsvp_status, no_of_guest = :no_of_guest WHERE fullname = :fullname";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'rsvp_status' => $rsvp_status,
                    'no_of_guest' => ($rsvp_status === 'No' ? null : $no_of_guests),
                    'fullname' => $fullname
                ]);
                echo "<p class='message success'>Thank you for your response! Your RSVP has been updated successfully.</p>";
            } else {
                echo "<p class='message error'>Sorry, your name does not appear to be on the list. Please ensure your full name is spelled correctly.</p>";
            }
        } catch (PDOException $e) {
            echo "<p class='message error'>Error: " . $e->getMessage() . "</p>";
        }
        ?>
        <a href="index.html" class="button">Back to Home</a>
    </div>
</body>
</html>
