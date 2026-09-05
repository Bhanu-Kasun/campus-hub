<?php
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';
requireAdminLogin();

$pageTitle = "Manage Students";
$message = '';
$messageType = '';

// ---- DELETE ----
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM students WHERE student_id = $deleteId");
    $message = "Student deleted.";
    $messageType = "success-msg";
}

// ---- UPDATE (admin can edit a student's basic info) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $editId    = isset($_POST['student_id']) ? (int)$_POST['student_id'] : 0;
    $fullName  = cleanInput($conn, $_POST['full_name'] ?? '');
    $email     = cleanInput($conn, $_POST['email'] ?? '');
    $contactNo = cleanInput($conn, $_POST['contact_no'] ?? '');

    if (empty($fullName) || empty($email) || !isValidEmail($email)) {
        $message = "A valid name and email are required.";
        $messageType = "error-msg";
    } elseif ($editId > 0) {
        $query = "UPDATE students SET full_name='$fullName', email='$email', contact_no='$contactNo' WHERE student_id = $editId";
        if (mysqli_query($conn, $query)) {
            $message = "Student updated successfully.";
            $messageType = "success-msg";
        } else {
            $message = "Update failed: " . mysqli_error($conn);
            $messageType = "error-msg";
        }
    }
}

// ---- SELECT (for edit form pre-fill) ----
$editStudent = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $r = mysqli_query($conn, "SELECT * FROM students WHERE student_id = $editId");
    $editStudent = mysqli_fetch_assoc($r);
}

// ---- SELECT (list all, with optional search) ----
$search = isset($_GET['search']) ? cleanInput($conn, $_GET['search']) : '';
$query = "SELECT * FROM students";
if (!empty($search)) {
    $query .= " WHERE full_name LIKE '%$search%' OR email LIKE '%$search%'";
}
$query .= " ORDER BY registered_on DESC";
$students = mysqli_query($conn, $query);

include '../includes/header2.php';
?>

<h1><center>Manage Students</center></h1>

<?php if ($message): ?>
    <div class="<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<?php if ($editStudent): ?>
<form class="form-box" method="POST" action="manage_students.php">
    <h3>Edit Student</h3>
    <input type="hidden" name="student_id" value="<?php echo $editStudent['student_id']; ?>">

    <label>Full Name</label>
    <input type="text" name="full_name" value="<?php echo htmlspecialchars($editStudent['full_name']); ?>" required>

    <label>Email</label>
    <input type="email" name="email" value="<?php echo htmlspecialchars($editStudent['email']); ?>" required>

    <label>Contact Number</label>
    <input type="text" name="contact_no" value="<?php echo htmlspecialchars($editStudent['contact_no']); ?>">

    <button type="submit" class="btn" style="margin-top:1.2rem;">Update Student</button>
</form>
<?php endif; ?>

<form method="GET" action="manage_students.php" style="display:flex; gap:1rem; margin-top:1.5rem;">
    <input type="text" name="search" placeholder="Search by name or email..." value="<?php echo htmlspecialchars($search); ?>" style="flex:1; padding:0.6rem; border:1px solid #ccc; border-radius:5px;">
    <button type="submit" class="btn">Search</button>
</form>

<h2 style="margin-top:2rem;">All Students</h2>
<table style="width:100%; background:#fff; border-collapse:collapse; margin-top:1rem;">
    <tr style="background:#1f3a5f; color:#fff;">
        <th style="padding:0.6rem; text-align:left;">Name</th>
        <th style="padding:0.6rem; text-align:left;">Email</th>
        <th style="padding:0.6rem; text-align:left;">Contact</th>
        <th style="padding:0.6rem; text-align:left;">Registered On</th>
        <th style="padding:0.6rem; text-align:left;">Actions</th>
    </tr>
    <?php if ($students && mysqli_num_rows($students) > 0): ?>
        <?php while ($s = mysqli_fetch_assoc($students)): ?>
            <tr style="border-bottom:1px solid #eee;">
                <td style="padding:0.6rem;"><?php echo htmlspecialchars($s['full_name']); ?></td>
                <td style="padding:0.6rem;"><?php echo htmlspecialchars($s['email']); ?></td>
                <td style="padding:0.6rem;"><?php echo htmlspecialchars($s['contact_no']); ?></td>
                <td style="padding:0.6rem;"><?php echo formatEventDate($s['registered_on']); ?></td>
                <td style="padding:0.6rem;">
                    <a href="manage_students.php?edit=<?php echo $s['student_id']; ?>">Edit</a> |
                    <a href="manage_students.php?delete=<?php echo $s['student_id']; ?>" onclick="return confirm('Delete this student?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="5" style="padding:0.6rem;">No students found.</td></tr>
    <?php endif; ?>
</table>

<?php include '../includes/footer.php'; ?>