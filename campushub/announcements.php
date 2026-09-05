<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

$pageTitle = "Announcements";

$query = "SELECT a.title, a.content, a.posted_on, ad.full_name AS posted_by_name
          FROM announcements a
          LEFT JOIN admins ad ON a.posted_by = ad.admin_id
          ORDER BY a.posted_on DESC";
$result = mysqli_query($conn, $query);

include 'includes/header.php';
?>

<h1><center>Announcements</center></h1>

<?php if ($result && mysqli_num_rows($result) > 0): ?>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <div class="card" style="margin-top:1.5rem;">
            <div class="card-body">
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                <small>
                    Posted <?php echo formatEventDate($row['posted_on']); ?>
                    <?php if (!empty($row['posted_by_name'])): ?>
                        by <?php echo htmlspecialchars($row['posted_by_name']); ?>
                    <?php endif; ?>
                </small>
            </div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No announcements yet. Check back soon!</p>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
