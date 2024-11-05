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
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch the current password hash from the database
    $sql = "SELECT password FROM user_auth WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $_SESSION['id']]);
    $currentPasswordHash = $stmt->fetchColumn();

    if (!empty($password)) {
        // Check if the new password is the same as the current one
        if (password_verify($password, $currentPasswordHash)) {
            $_SESSION['update_error'] = 'New password cannot be the same as the current password.';
            header("Location: profile.php");
            exit();
        }

        // Hash the new password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $sql = "UPDATE user_auth SET username = :username, email = :email, password = :password WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword,
            'id' => $_SESSION['id']
        ]);
    } else {
        $sql = "UPDATE user_auth SET username = :username, email = :email WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'id' => $_SESSION['id']
        ]);
    }

    $_SESSION['update_success'] = 'Profile updated successfully!';
    header("Location: profile.php");
    exit();

} catch (PDOException $e) {
    $_SESSION['update_error'] = "Update failed: " . $e->getMessage();
    header("Location: profile.php");
    exit();
}
?>
