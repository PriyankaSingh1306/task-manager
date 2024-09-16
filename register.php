<?php
include '../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Both fields are required.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif (register($username, $password)) {
        header('Location: login.php');
        exit();
    } else {
        $error = "Registration failed.";
    }
}
?>
<form method="POST">
    <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($username ?? ''); ?>" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Register</button>
</form>
<?php if (isset($error)) echo "<p>$error</p>"; ?>
