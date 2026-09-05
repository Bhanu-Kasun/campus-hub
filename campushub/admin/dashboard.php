<?php
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';
requireAdminLogin();

$pageTitle = "Admin Dashboard";

$studentCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM students"))['c'];
$eventCount   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM events"))['c'];
$regCount     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM registrations"))['c'];

include '../includes/header2.php';


?>

<h1>Admin Dashboard</h1>
<p>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>.</p>

<div class="card-grid">
    <div class="card"><div class="card-body"><h3><?php echo $studentCount; ?></h3><p>Registered Students</p></div></div>
    <div class="card"><div class="card-body"><h3><?php echo $eventCount; ?></h3><p>Total Events</p></div></div>
    <div class="card"><div class="card-body"><h3><?php echo $regCount; ?></h3><p>Event Registrations</p></div></div>
</div>

<div style="margin-top:2rem;">
    <a class="btn" href="manage_events.php">Manage Events</a>
    <a class="btn" href="manage_students.php">Manage Students</a>
    <a class="btn" href="manage_registrations.php">Manage Registrations</a>
    <a class="btn" href="manage_announcements.php">Manage Announcements</a>
    <a class="btn" href="export_events_xml.php">Export Events (XML)</a>
    <a class="btn" href="manage_media.php">Manage Media</a>
</div>

<?php include '../includes/footer.php'; ?>
