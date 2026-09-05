<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

$pageTitle = "Home";

// Fetch latest 3 upcoming events (dynamic content via PHP + DB)
$eventQuery = "SELECT event_id, title, event_date, venue, poster_image 
               FROM events 
               WHERE event_date >= CURDATE() 
               ORDER BY event_date ASC 
               LIMIT 3";
$eventResult = mysqli_query($conn, $eventQuery);

// Fetch latest 2 announcements
$annQuery = "SELECT title, content, posted_on FROM announcements ORDER BY posted_on DESC LIMIT 2";
$annResult = mysqli_query($conn, $annQuery);

include 'includes/header.php';
?>

<section class="hero">
    <h1>Welcome to CampusHub</h1>
    <p>Your one place for club events, workshops, competitions, and student communities.</p>
</section>

<section>
    <h2>Upcoming Events</h2>
    <div class="card-grid">
        <?php if ($eventResult && mysqli_num_rows($eventResult) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($eventResult)): ?>
                <div class="card">
                    <img src="<?php echo BASE_URL; ?>/uploads/events/<?php echo htmlspecialchars($row['poster_image'] ?: 'default-event.jpg'); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                    <div class="card-body">
                        <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                        <p><?php echo formatEventDate($row['event_date']); ?> — <?php echo htmlspecialchars($row['venue']); ?></p>
                        <a class="btn" href="<?php echo BASE_URL; ?>/event_details.php?id=<?php echo (int)$row['event_id']; ?>">View Details</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No upcoming events at the moment. Check back soon!</p>
        <?php endif; ?>
    </div>
</section>

<section>
    <h2>Latest Announcements</h2>
    <?php if ($annResult && mysqli_num_rows($annResult) > 0): ?>
        <?php while ($ann = mysqli_fetch_assoc($annResult)): ?>
            <div class="card">
                <div class="card-body">
                    <h3><?php echo htmlspecialchars($ann['title']); ?></h3>
                    <p><?php echo nl2br(htmlspecialchars($ann['content'])); ?></p>
                    <small><?php echo formatEventDate($ann['posted_on']); ?></small>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No announcements yet.</p>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>
