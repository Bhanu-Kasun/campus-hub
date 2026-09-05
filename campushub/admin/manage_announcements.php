<?php
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';
requireAdminLogin();

$pageTitle = "Manage Announcements";
$message = '';
$messageType = '';
$adminId = $_SESSION['admin_id'];

// ---- DELETE ----
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM announcements WHERE announcement_id = $deleteId");
    $message = "Announcement deleted.";
    $messageType = "success-msg";
}

// ---- INSERT / UPDATE ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = cleanInput($conn, $_POST['title'] ?? '');
    $content = cleanInput($conn, $_POST['content'] ?? '');
    $editId  = isset($_POST['announcement_id']) ? (int)$_POST['announcement_id'] : 0;

    if (empty($title) || empty($content)) {
        $message = "Title and content are required.";
        $messageType = "error-msg";
    } else {
        if ($editId > 0) {
            $query = "UPDATE announcements SET title='$title', content='$content' WHERE announcement_id = $editId";
            $action = "updated";
        } else {
            $query = "INSERT INTO announcements (title, content, posted_by) VALUES ('$title', '$content', $adminId)";
            $action = "posted";
        }

        if (mysqli_query($conn, $query)) {
            $message = "Announcement $action successfully.";
            $messageType = "success-msg";
        } else {
            $message = "Operation failed: " . mysqli_error($conn);
            $messageType = "error-msg";
        }
    }
}

// ---- SELECT (for edit form pre-fill) ----
$editAnnouncement = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $r = mysqli_query($conn, "SELECT * FROM announcements WHERE announcement_id = $editId");
    $editAnnouncement = mysqli_fetch_assoc($r);
}

// ---- SELECT (list all) ----
$announcements = mysqli_query($conn, "SELECT * FROM announcements ORDER BY posted_on DESC");

include '../includes/header2.php';
?>

<h1><center>Manage Announcements</center></h1>

<?php if ($message): ?>
    <div class="<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<form class="form-box" method="POST" action="manage_announcements.php">
    <h3><?php echo $editAnnouncement ? "Edit Announcement" : "Post New Announcement"; ?></h3>
    <?php if ($editAnnouncement): ?>
        <input type="hidden" name="announcement_id" value="<?php echo $editAnnouncement['announcement_id']; ?>">
    <?php endif; ?>

    <label>Title</label>
    <input type="text" name="title" value="<?php echo htmlspecialchars($editAnnouncement['title'] ?? ''); ?>" required>

    <label>Content</label>
    <textarea name="content" rows="4" required><?php echo htmlspecialchars($editAnnouncement['content'] ?? ''); ?></textarea>

    <button type="submit" class="btn" style="margin-top:1.2rem;"><?php echo $editAnnouncement ? "Update Announcement" : "Post Announcement"; ?></button>
</form>

<h2 style="margin-top:2rem;">All Announcements</h2>
<table style="width:100%; background:#fff; border-collapse:collapse; margin-top:1rem;">
    <tr style="background:#1f3a5f; color:#fff;">
        <th style="padding:0.6rem; text-align:left;">Title</th>
        <th style="padding:0.6rem; text-align:left;">Content</th>
        <th style="padding:0.6rem; text-align:left;">Posted On</th>
        <th style="padding:0.6rem; text-align:left;">Actions</th>
    </tr>
    <?php if ($announcements && mysqli_num_rows($announcements) > 0): ?>
        <?php while ($a = mysqli_fetch_assoc($announcements)): ?>
            <tr style="border-bottom:1px solid #eee;">
                <td style="padding:0.6rem;"><?php echo htmlspecialchars($a['title']); ?></td>
                <td style="padding:0.6rem;"><?php echo htmlspecialchars(mb_strimwidth($a['content'], 0, 60, '...')); ?></td>
                <td style="padding:0.6rem;"><?php echo formatEventDate($a['posted_on']); ?></td>
                <td style="padding:0.6rem;">
                    <a href="manage_announcements.php?edit=<?php echo $a['announcement_id']; ?>">Edit</a> |
                    <a href="manage_announcements.php?delete=<?php echo $a['announcement_id']; ?>" onclick="return confirm('Delete this announcement?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="4" style="padding:0.6rem;">No announcements posted yet.</td></tr>
    <?php endif; ?>
</table>

<?php include '../includes/footer.php'; ?>