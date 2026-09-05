<?php
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

$pageTitle = "Admin Login";
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = cleanInput($conn, $_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $query = "SELECT admin_id, username, password, full_name FROM admins WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $admin = mysqli_fetch_assoc($result);
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id']   = $admin['admin_id'];
            $_SESSION['admin_name'] = $admin['full_name'];
            header("Location: dashboard.php");
            exit();
        }
    }
    $errors[] = "Invalid admin credentials.";
}

include '../includes/header.php';
?>

<h1><center>Admin Login</center></h1>

<?php if (!empty($errors)): ?>
    <div class="error-msg">
        <?php foreach ($errors as $err): ?>
            <p><?php echo htmlspecialchars($err); ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form class="form-box" method="POST" action="login.php">
    <label for="username">Username</label>
    <input type="text" id="username" name="username" required>

    <label for="password">Password</label>
    <input type="password" id="password" name="password" required>

    <button type="submit" class="btn" style="margin-top:1.2rem;">Login</button>
</form>

<?php include '../includes/footer.php'; ?>
