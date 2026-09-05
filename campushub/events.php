<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

$pageTitle = "Events";

// Search / filter (Scenario-based feature: search)
$search = isset($_GET['search']) ? cleanInput($conn, $_GET['search']) : '';

$query = "SELECT e.event_id, e.title, e.description, e.event_date, e.venue, e.poster_image, c.category_name
          FROM events e
          LEFT JOIN event_categories c ON e.category_id = c.category_id";

if (!empty($search)) {
    $query .= " WHERE e.title LIKE '%$search%' OR e.description LIKE '%$search%'";
}
$query .= " ORDER BY e.event_date ASC";

$result = mysqli_query($conn, $query);

include 'includes/header.php';
?>

<h1><center>All Events</center></h1>

<form class="form-box" method="GET" action="events.php" style="max-width:100%; display:flex; gap:1rem; align-items:center;">
    <input type="text" name="search" placeholder="Search events..." value="<?php echo htmlspecialchars($search); ?>" style="flex:1;">
    <button type="submit" class="btn">Search</button>
</form>

<div class="card-grid">
    <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="card">
                <img src="<?php echo BASE_URL; ?>/uploads/events/<?php echo htmlspecialchars($row['poster_image'] ?: 'default-event.jpg'); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                <div class="card-body">
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p><small><?php echo htmlspecialchars($row['category_name'] ?? 'General'); ?></small></p>
                    <p><?php echo formatEventDate($row['event_date']); ?> — <?php echo htmlspecialchars($row['venue']); ?></p>
                    <a class="btn" href="<?php echo BASE_URL; ?>/event_details.php?id=<?php echo (int)$row['event_id']; ?>">View & Register</a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No events found<?php echo !empty($search) ? " for \"" . htmlspecialchars($search) . "\"" : ""; ?>.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
