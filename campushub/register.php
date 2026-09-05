<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

$pageTitle = "Register";
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = cleanInput($conn, $_POST['full_name'] ?? '');
    $email    = cleanInput($conn, $_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $contact  = cleanInput($conn, $_POST['contact_no'] ?? '');

    // ---- Validation ----
    if (empty($fullName)) $errors[] = "Full name is required.";
    if (empty($email) || !isValidEmail($email)) $errors[] = "A valid email is required.";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";
    if (empty($contact)) {
        $errors[] = "Contact number is required.";
    } elseif (!preg_match('/^[0-9]{10}$/', $contact)) {
        $errors[] = "Contact number must be exactly 10 digits.";
    }

    if (empty($errors)) {
        // Check for existing email
        $checkQuery = "SELECT student_id FROM students WHERE email = '$email'";
        $checkResult = mysqli_query($conn, $checkQuery);

        if (mysqli_num_rows($checkResult) > 0) {
            $errors[] = "An account with this email already exists.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $insertQuery = "INSERT INTO students (full_name, email, password, contact_no) 
                            VALUES ('$fullName', '$email', '$hashedPassword', '$contact')";

            if (mysqli_query($conn, $insertQuery)) {
                $success = true;
            } else {
                $errors[] = "Registration failed. Please try again.";
            }
        }
    }
}

include 'includes/header.php';
?>

<h1><center>Student Registration</center></h1>

<?php if ($success): ?>
    <p class="success-msg">Registration successful! You can now <a href="<?php echo BASE_URL; ?>/login.php">log in</a>.</p>
<?php else: ?>

    <?php if (!empty($errors)): ?>
        <div class="error-msg">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?php echo htmlspecialchars($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form class="form-box" method="POST" action="register.php">
        <label for="full_name">Full Name</label>
        <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <label for="contact_no">Contact Number</label>
        <input type="text" id="contact_no" name="contact_no" value="<?php echo htmlspecialchars($_POST['contact_no'] ?? ''); ?>" pattern="[0-9]{10}" maxlength="10" inputmode="numeric" title="Enter exactly 10 digits" required>

        <button type="submit" class="btn" style="margin-top:1.2rem;">Register</button>
    </form>

<?php endif; ?>

<?php include 'includes/footer.php'; ?>