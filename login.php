<?php
session_start();
include '../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Both fields are required.";
    } elseif (login($username, $password)) {
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Invalid credentials.";
    }
}
?>
<form method="POST">
    <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($username ?? ''); ?>" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
</form>
<?php if (isset($error)) echo "<p>$error</p>"; ?>
