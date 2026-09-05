<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

$eventId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$messageType = '';

// Handle registration submit (Insert operation)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_event'])) {
    requireStudentLogin();
    $studentId = $_SESSION['student_id'];

    $checkQuery = "SELECT registration_id FROM registrations WHERE student_id = $studentId AND event_id = $eventId";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        $message = "You are already registered for this event.";
        $messageType = "error-msg";
    } else {
        $insertQuery = "INSERT INTO registrations (student_id, event_id) VALUES ($studentId, $eventId)";
        if (mysqli_query($conn, $insertQuery)) {
            $message = "You have successfully registered for this event!";
            $messageType = "success-msg";
        } else {
            $message = "Registration failed. Please try again.";
            $messageType = "error-msg";
        }
    }
}

$query = "SELECT e.*, c.category_name FROM events e 
          LEFT JOIN event_categories c ON e.category_id = c.category_id 
          WHERE e.event_id = $eventId";
$result = mysqli_query($conn, $query);
$event = $result ? mysqli_fetch_assoc($result) : null;

$pageTitle = $event ? $event['title'] : "Event Not Found";

// Fetch any media (video/audio) linked to this event
$mediaQuery = "SELECT file_name, file_type FROM media WHERE event_id = $eventId";
$mediaResult = mysqli_query($conn, $mediaQuery);

include 'includes/header.php';
?>

<?php if ($event): ?>
    <h1><?php echo htmlspecialchars($event['title']); ?></h1>
    <p><small><?php echo htmlspecialchars($event['category_name'] ?? 'General'); ?></small></p>
    <img src="<?php echo BASE_URL; ?>/uploads/events/<?php echo htmlspecialchars($event['poster_image'] ?: 'default-event.jpg'); ?>" style="max-width:100%; border-radius:8px; margin:1rem 0;">
    <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
    <p><strong>Date:</strong> <?php echo formatEventDate($event['event_date']); ?></p>
    <p><strong>Venue:</strong> <?php echo htmlspecialchars($event['venue']); ?></p>

    <?php if ($message): ?>
        <div class="<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if (isStudentLoggedIn()): ?>
        <form method="POST" action="event_details.php?id=<?php echo $eventId; ?>">
            <button type="submit" name="register_event" class="btn" style="margin-top:1rem;">Register for this Event</button>
        </form>
    <?php else: ?>
        <p style="margin-top:1rem;"><a href="<?php echo BASE_URL; ?>/login.php">Log in</a> to register for this event.</p>
    <?php endif; ?>

    <!-- Multimedia section -->
    <?php if ($mediaResult && mysqli_num_rows($mediaResult) > 0): ?>
        <h2 style="margin-top:2rem;">Event Media</h2>
        <?php while ($media = mysqli_fetch_assoc($mediaResult)): ?>
            <?php if ($media['file_type'] === 'video'): ?>
                <video controls style="max-width:100%; margin-top:1rem;">
                    <source src="<?php echo BASE_URL; ?>/uploads/events/<?php echo htmlspecialchars($media['file_name']); ?>" type="video/mp4">
                    Your browser does not support video playback.
                </video>
            <?php elseif ($media['file_type'] === 'audio'): ?>
                <audio controls style="margin-top:1rem;">
                    <source src="<?php echo BASE_URL; ?>/uploads/events/<?php echo htmlspecialchars($media['file_name']); ?>" type="audio/mpeg">
                    Your browser does not support audio playback.
                </audio>
            <?php endif; ?>
        <?php endwhile; ?>
    <?php endif; ?>

<?php else: ?>
    <h1>Event Not Found</h1>
    <p>The event you're looking for doesn't exist.</p>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
