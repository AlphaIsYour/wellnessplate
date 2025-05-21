<?php
// File: admin/login.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php"); // Asumsi dashboard.php ada di folder yang sama
    exit;
}

$page_title = 'Login Admin - WellnessPlate';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link rel="stylesheet" href="style.css"> <?php // Pastikan path style.css benar relatif terhadap login.php ?>
</head>
<body>
    <div class="login-container">
        <h2>Login Admin</h2>
        <form action="proses_login.php" method="POST">
            <?php
            if (isset($_GET['error'])) {
                echo "<p class='error-message'>" . htmlspecialchars($_GET['error']) . "</p>";
            }
            if (isset($_GET['message'])) {
                echo "<p class='success-message'>" . htmlspecialchars($_GET['message']) . "</p>";
            }
            ?>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
    </div>
    <?php /* Jika ada script.js khusus untuk login: <script src="script.js"></script> */ ?>
</body>
</html>