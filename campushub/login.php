<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

$pageTitle = "Login";
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = cleanInput($conn, $_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $errors[] = "Email and password are required.";
    } else {
        $query = "SELECT student_id, full_name, password FROM students WHERE email = '$email'";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) === 1) {
            $student = mysqli_fetch_assoc($result);

            if (password_verify($password, $student['password'])) {
                $_SESSION['student_id']   = $student['student_id'];
                $_SESSION['student_name'] = $student['full_name'];
                header("Location: " . BASE_URL . "/index.php");
                exit();
            } else {
                $errors[] = "Incorrect email or password.";
            }
        } else {
            $errors[] = "Incorrect email or password.";
        }
    }
}

include 'includes/header.php';
?>

<h1><center>Student Login</center></h1>

<?php if (!empty($errors)): ?>
    <div class="error-msg">
        <ul>
            <?php foreach ($errors as $err): ?>
                <li><?php echo htmlspecialchars($err); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form class="form-box" method="POST" action="login.php">
    <label for="email">Email</label>
    <input type="email" id="email" name="email" required>

    <label for="password">Password</label>
    <input type="password" id="password" name="password" required>

    <button type="submit" class="btn" style="margin-top:1.2rem;">Login</button>
</form>
<p style="text-align:center; margin-top:1rem;">No account? <a href="<?php echo BASE_URL; ?>/register.php">Register here</a></p>

<?php include 'includes/footer.php'; ?>
