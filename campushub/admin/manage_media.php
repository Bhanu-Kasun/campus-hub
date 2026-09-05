<?php
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Only allow admins
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$pageTitle = "Manage Media";
$errors = [];
$success = "";

// Handle delete
if (isset($_GET['delete_id'])) {
    $deleteId = (int)$_GET['delete_id'];
    $res = mysqli_query($conn, "SELECT file_name FROM media WHERE media_id=$deleteId");
    if ($row = mysqli_fetch_assoc($res)) {
        $filePath = "../uploads/events/" . $row['file_name'];
        if (file_exists($filePath)) {
            unlink($filePath); // remove actual file
        }
        mysqli_query($conn, "DELETE FROM media WHERE media_id=$deleteId");
        $success = "✅ File deleted successfully!";
    } else {
        $errors[] = "File not found in database.";
    }
}

// Handle multiple uploads
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = (int)($_POST['event_id'] ?? 0);
    $files = $_FILES['media_file'];

    if ($event_id > 0 && !empty($files['name'][0])) {
        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $targetDir = "../uploads/events/";
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }

                $fileName = time() . "_" . basename($files['name'][$i]);
                $targetFile = $targetDir . $fileName;

                if (move_uploaded_file($files['tmp_name'][$i], $targetFile)) {
                    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    $fileType = 'image';
                    if (in_array($ext, ['mp4','webm','ogg'])) $fileType = 'video';
                    if (in_array($ext, ['mp3','wav'])) $fileType = 'audio';

                    $stmt = $conn->prepare("INSERT INTO media (event_id, file_name, file_type, uploaded_by, uploaded_on) VALUES (?, ?, ?, ?, NOW())");
                    $stmt->bind_param("issi", $event_id, $fileName, $fileType, $_SESSION['admin_id']);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    $errors[] = "Upload failed for " . htmlspecialchars($files['name'][$i]);
                }
            }
        }
    } else {
        $errors[] = "Please select an event and at least one file.";
    }
}

// Fetch events for dropdown
$eventsResult = mysqli_query($conn, "SELECT event_id, title FROM events ORDER BY event_date DESC");

// Fetch existing media grouped by event
$mediaResult = mysqli_query($conn, "SELECT m.*, e.title AS event_title 
                                    FROM media m 
                                    LEFT JOIN events e ON m.event_id = e.event_id 
                                    ORDER BY m.media_id DESC");

include '../includes/header2.php';
?>

<h1><center>Manage Media</center></h1>

<?php if (!empty($errors)): ?>
    <div class="error-msg">
        <?php foreach ($errors as $err): ?>
            <p><?php echo htmlspecialchars($err); ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="success-msg">
        <p><?php echo htmlspecialchars($success); ?></p>
    </div>
<?php endif; ?>

<!-- Upload Form -->
<form class="form-box" method="POST" enctype="multipart/form-data">
    <label for="event_id">Select Event</label>
    <select name="event_id" id="event_id" required>
        <option value="">-- Choose Event --</option>
        <?php while ($ev = mysqli_fetch_assoc($eventsResult)) { ?>
            <option value="<?php echo $ev['event_id']; ?>">
                <?php echo htmlspecialchars($ev['title']); ?>
            </option>
        <?php } ?>
    </select>

    <label for="media_file">Upload Files</label>
    <input type="file" id="media_file" name="media_file[]" multiple required>

    <button type="submit" class="btn" style="margin-top:1.2rem;">Upload</button>
</form>

<hr>

<!-- Existing Media -->
<h2>Uploaded Media</h2>
<?php 
$currentEvent = null;
while ($row = mysqli_fetch_assoc($mediaResult)) {
    if ($currentEvent !== $row['event_title']) {
        if ($currentEvent !== null) echo "</div>"; // close previous event block
        $currentEvent = $row['event_title'];
        echo "<h3>" . htmlspecialchars($currentEvent ?? 'General') . "</h3><div style='display:flex;flex-wrap:wrap;gap:10px;'>";
    }
    $ext = pathinfo($row['file_name'], PATHINFO_EXTENSION);
    echo "<div style='border:1px solid #ccc;padding:5px;'>";
    if ($row['file_type'] === 'image') {
        echo "<img src='../uploads/events/{$row['file_name']}' style='max-width:150px;max-height:150px;'><br>";
    } elseif ($row['file_type'] === 'video') {
        echo "<video src='../uploads/events/{$row['file_name']}' controls style='max-width:150px;max-height:150px;'></video><br>";
    } elseif ($row['file_type'] === 'audio') {
        echo "<audio src='../uploads/events/{$row['file_name']}' controls></audio><br>";
    }
    echo "<a href='manage_media.php?delete_id={$row['media_id']}' onclick=\"return confirm('Delete this file?');\">❌ Delete</a>";
    echo "</div>";
}
if ($currentEvent !== null) echo "</div>";
?>

<?php include '../includes/footer.php'; ?>
