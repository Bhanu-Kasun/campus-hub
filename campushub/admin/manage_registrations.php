<?php
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';
requireAdminLogin();

$pageTitle = "Manage Registrations";
$message = '';
$messageType = '';

// ---- DELETE ----
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM registrations WHERE registration_id = $deleteId");
    $message = "Registration removed.";
    $messageType = "success-msg";
}

// ---- UPDATE STATUS (e.g. Confirmed / Cancelled) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registration_id'])) {
    $regId  = (int)$_POST['registration_id'];
    $status = cleanInput($conn, $_POST['status'] ?? 'Confirmed');
    $query = "UPDATE registrations SET status = '$status' WHERE registration_id = $regId";
    if (mysqli_query($conn, $query)) {
        $message = "Registration status updated.";
        $messageType = "success-msg";
    } else {
        $message = "Update failed: " . mysqli_error($conn);
        $messageType = "error-msg";
    }
}

// ---- SELECT (list all registrations with student + event info) ----
$query = "SELECT r.registration_id, r.registered_on, r.status,
                 s.full_name, s.email,
                 e.title AS event_title, e.event_date
          FROM registrations r
          JOIN students s ON r.student_id = s.student_id
          JOIN events e ON r.event_id = e.event_id
          ORDER BY r.registered_on DESC";
$registrations = mysqli_query($conn, $query);

include '../includes/header2.php';
?>

<h1><center>Manage Registrations</center></h1>

<?php if ($message): ?>
    <div class="<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<table style="width:100%; background:#fff; border-collapse:collapse; margin-top:1.5rem;">
    <tr style="background:#1f3a5f; color:#fff;">
        <th style="padding:0.6rem; text-align:left;">Student</th>
        <th style="padding:0.6rem; text-align:left;">Event</th>
        <th style="padding:0.6rem; text-align:left;">Event Date</th>
        <th style="padding:0.6rem; text-align:left;">Registered On</th>
        <th style="padding:0.6rem; text-align:left;">Status</th>
        <th style="padding:0.6rem; text-align:left;">Actions</th>
    </tr>
    <?php if ($registrations && mysqli_num_rows($registrations) > 0): ?>
        <?php while ($r = mysqli_fetch_assoc($registrations)): ?>
            <tr style="border-bottom:1px solid #eee;">
                <td style="padding:0.6rem;"><?php echo htmlspecialchars($r['full_name']); ?><br><small><?php echo htmlspecialchars($r['email']); ?></small></td>
                <td style="padding:0.6rem;"><?php echo htmlspecialchars($r['event_title']); ?></td>
                <td style="padding:0.6rem;"><?php echo formatEventDate($r['event_date']); ?></td>
                <td style="padding:0.6rem;"><?php echo formatEventDate($r['registered_on']); ?></td>
                <td style="padding:0.6rem;">
                    <form method="POST" action="manage_registrations.php" style="display:flex; gap:0.4rem; align-items:center;">
                        <input type="hidden" name="registration_id" value="<?php echo $r['registration_id']; ?>">
                        <select name="status" onchange="this.form.submit()" style="padding:0.3rem;">
                            <option value="Confirmed" <?php echo $r['status'] === 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                            <option value="Cancelled" <?php echo $r['status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            <option value="Attended" <?php echo $r['status'] === 'Attended' ? 'selected' : ''; ?>>Attended</option>
                        </select>
                    </form>
                </td>
                <td style="padding:0.6rem;">
                    <a href="manage_registrations.php?delete=<?php echo $r['registration_id']; ?>" onclick="return confirm('Remove this registration?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="6" style="padding:0.6rem;">No registrations yet.</td></tr>
    <?php endif; ?>
</table>

<?php include '../includes/footer.php'; ?>