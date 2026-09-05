<?php
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';
requireAdminLogin();

$pageTitle = "Manage Events";
$message = '';
$messageType = '';
$adminId = $_SESSION['admin_id'];

// ---- DELETE ----
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM events WHERE event_id = $deleteId");
    $message = "Event deleted.";
    $messageType = "success-msg";
}

// ---- INSERT / UPDATE ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = cleanInput($conn, $_POST['title'] ?? '');
    $description = cleanInput($conn, $_POST['description'] ?? '');
    $eventDate   = cleanInput($conn, $_POST['event_date'] ?? '');
    $venue       = cleanInput($conn, $_POST['venue'] ?? '');
    $categoryId  = isset($_POST['category_id']) && $_POST['category_id'] !== '' ? (int)$_POST['category_id'] : null;
    $clubId      = isset($_POST['club_id']) && $_POST['club_id'] !== '' ? (int)$_POST['club_id'] : null;
    $editId      = isset($_POST['event_id']) ? (int)$_POST['event_id'] : 0;

    if (empty($title) || empty($eventDate)) {
        $message = "Title and date are required.";
        $messageType = "error-msg";
    } else {
        $posterClause = '';
        if (!empty($_FILES['poster_image']['name'])) {
            $upload = handleFileUpload('poster_image', '../uploads/events');
            if ($upload['success']) {
                $posterClause = ", poster_image = '" . $upload['filename'] . "'";
            }
        }

        $categoryValue = $categoryId !== null ? $categoryId : 'NULL';
        $clubValue = $clubId !== null ? $clubId : 'NULL';

        if ($editId > 0) {
            // UPDATE
            $query = "UPDATE events SET title='$title', description='$description', 
                      event_date='$eventDate', venue='$venue', category_id=$categoryValue, club_id=$clubValue $posterClause 
                      WHERE event_id = $editId";
            $action = "updated";
        } else {
            // INSERT
            $query = "INSERT INTO events (title, description, event_date, venue, category_id, club_id, created_by) 
                      VALUES ('$title', '$description', '$eventDate', '$venue', $categoryValue, $clubValue, $adminId)";
            $action = "created";
        }

        if (mysqli_query($conn, $query)) {
            $message = "Event $action successfully.";
            $messageType = "success-msg";
        } else {
            $message = "Operation failed: " . mysqli_error($conn);
            $messageType = "error-msg";
        }
    }
}

// ---- SELECT (for edit form pre-fill) ----
$editEvent = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $r = mysqli_query($conn, "SELECT * FROM events WHERE event_id = $editId");
    $editEvent = mysqli_fetch_assoc($r);
}

// ---- SELECT (list all) ----
$events = mysqli_query($conn, "SELECT e.*, c.category_name, cl.club_name FROM events e 
                                LEFT JOIN event_categories c ON e.category_id = c.category_id 
                                LEFT JOIN clubs cl ON e.club_id = cl.club_id
                                ORDER BY e.event_date DESC");

// ---- Fetch categories for the dropdown ----
$categories = mysqli_query($conn, "SELECT category_id, category_name FROM event_categories ORDER BY category_name ASC");

// ---- Fetch clubs for the dropdown ----
$clubs = mysqli_query($conn, "SELECT club_id, club_name FROM clubs ORDER BY club_name ASC");

include '../includes/header2.php';
?>

<h1>Manage Events</h1>

<?php if ($message): ?>
    <div class="<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<form class="form-box" method="POST" action="manage_events.php" enctype="multipart/form-data">
    <h3><?php echo $editEvent ? "Edit Event" : "Add New Event"; ?></h3>
    <?php if ($editEvent): ?>
        <input type="hidden" name="event_id" value="<?php echo $editEvent['event_id']; ?>">
    <?php endif; ?>

    <label>Title</label>
    <input type="text" name="title" value="<?php echo htmlspecialchars($editEvent['title'] ?? ''); ?>" required>

    <label>Description</label>
    <textarea name="description" rows="4"><?php echo htmlspecialchars($editEvent['description'] ?? ''); ?></textarea>

    <label>Date</label>
    <input type="date" name="event_date" value="<?php echo htmlspecialchars($editEvent['event_date'] ?? ''); ?>" required>

    <label>Venue</label>
    <input type="text" name="venue" value="<?php echo htmlspecialchars($editEvent['venue'] ?? ''); ?>">

    <label>Category</label>
    <select name="category_id">
        <option value="">-- Select Category --</option>
        <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
            <option value="<?php echo $cat['category_id']; ?>" <?php echo (isset($editEvent['category_id']) && (int)$editEvent['category_id'] === (int)$cat['category_id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($cat['category_name']); ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label>Club</label>
    <select name="club_id">
        <option value="">-- Select Club --</option>
        <?php while ($cl = mysqli_fetch_assoc($clubs)): ?>
            <option value="<?php echo $cl['club_id']; ?>" <?php echo (isset($editEvent['club_id']) && (int)$editEvent['club_id'] === (int)$cl['club_id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($cl['club_name']); ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label>Poster Image</label>
    <input type="file" name="poster_image" accept="image/*">

    <button type="submit" class="btn" style="margin-top:1.2rem;"><?php echo $editEvent ? "Update Event" : "Add Event"; ?></button>
</form>

<h2 style="margin-top:2rem;">All Events</h2>
<table style="width:100%; background:#fff; border-collapse:collapse; margin-top:1rem;">
    <tr style="background:#1f3a5f; color:#fff;">
        <th style="padding:0.6rem; text-align:left;">Title</th>
        <th style="padding:0.6rem; text-align:left;">Category</th>
        <th style="padding:0.6rem; text-align:left;">Club</th>
        <th style="padding:0.6rem; text-align:left;">Date</th>
        <th style="padding:0.6rem; text-align:left;">Venue</th>
        <th style="padding:0.6rem; text-align:left;">Actions</th>
    </tr>
    <?php while ($e = mysqli_fetch_assoc($events)): ?>
        <tr style="border-bottom:1px solid #eee;">
            <td style="padding:0.6rem;"><?php echo htmlspecialchars($e['title']); ?></td>
            <td style="padding:0.6rem;"><?php echo htmlspecialchars($e['category_name'] ?? '—'); ?></td>
            <td style="padding:0.6rem;"><?php echo htmlspecialchars($e['club_name'] ?? '—'); ?></td>
            <td style="padding:0.6rem;"><?php echo formatEventDate($e['event_date']); ?></td>
            <td style="padding:0.6rem;"><?php echo htmlspecialchars($e['venue']); ?></td>
            <td style="padding:0.6rem;">
                <a href="manage_events.php?edit=<?php echo $e['event_id']; ?>">Edit</a> |
                <a href="manage_events.php?delete=<?php echo $e['event_id']; ?>" onclick="return confirm('Delete this event?');">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<?php include '../includes/footer.php'; ?>