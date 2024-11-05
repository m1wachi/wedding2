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

    // Get the form data
    $rsvp_status = $_POST['rsvp_status'];
    $num_guests = $_POST['num_guests'];

    // Update query for RSVP status and number of guests in the guest table
    $sql = "UPDATE guest SET rsvp_status = :rsvp_status, no_of_guest = :num_guests WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'rsvp_status' => $rsvp_status,
        'num_guests' => $num_guests,
        'email' => $_SESSION['email']
    ]);

    $_SESSION['rsvp_success'] = 'RSVP updated successfully!';
    header("Location: profile.php");
    exit();

} catch (PDOException $e) {
    $_SESSION['rsvp_error'] = "Failed to update RSVP: " . $e->getMessage();
    header("Location: profile.php");
    exit();
}
?>
