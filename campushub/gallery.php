<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

$pageTitle = "Gallery";

// Fetch media grouped by EVENT (each event gets its own carousel)
$query = "SELECT m.media_id, m.file_name, m.file_type, m.uploaded_on,
                 e.event_id, e.title AS event_title, c.category_name
          FROM media m
          LEFT JOIN events e ON m.event_id = e.event_id
          LEFT JOIN event_categories c ON e.category_id = c.category_id
          ORDER BY e.event_date DESC, m.uploaded_on DESC";
$result = mysqli_query($conn, $query);

// $eventGroups[event_title] = ['category' => ..., 'items' => [media rows]]
$eventGroups = [];
while ($row = mysqli_fetch_assoc($result)) {
    $event = $row['event_title'] ?? 'General';
    if (!isset($eventGroups[$event])) {
        $eventGroups[$event] = [
            'category' => $row['category_name'] ?? 'Uncategorised',
            'items' => []
        ];
    }
    $eventGroups[$event]['items'][] = $row;
}

include 'includes/header.php';
?>

<h1>Media Gallery</h1>
<p>Browse photos and videos from each CampusHub event.</p>

<?php if (!empty($eventGroups)): ?>

    <?php foreach ($eventGroups as $eventTitle => $group): ?>
        <?php $carouselId = 'carousel-' . preg_replace('/[^a-z0-9]+/i', '-', strtolower($eventTitle)); ?>
        <section class="gallery-category">
            <h2><?php echo htmlspecialchars($eventTitle); ?></h2>
            <p class="event-category-tag"><?php echo htmlspecialchars($group['category']); ?></p>

            <div class="carousel" id="<?php echo $carouselId; ?>">
                <button class="carousel-btn carousel-prev" onclick="slideCarousel('<?php echo $carouselId; ?>', -1)">&#10094;</button>

                <div class="carousel-track-wrap">
                    <div class="carousel-track">
                        <?php foreach ($group['items'] as $row): ?>
                            <div class="carousel-slide">
                                <?php if ($row['file_type'] === 'video'): ?>
                                    <video controls>
                                        <source src="<?php echo BASE_URL; ?>/uploads/events/<?php echo htmlspecialchars($row['file_name']); ?>" type="video/mp4">
                                    </video>
                                <?php elseif ($row['file_type'] === 'audio'): ?>
                                    <div class="carousel-audio">
                                        <audio controls style="width:100%;">
                                            <source src="<?php echo BASE_URL; ?>/uploads/events/<?php echo htmlspecialchars($row['file_name']); ?>" type="audio/mpeg">
                                        </audio>
                                    </div>
                                <?php else: ?>
                                    <img src="<?php echo BASE_URL; ?>/uploads/events/<?php echo htmlspecialchars($row['file_name']); ?>" alt="<?php echo htmlspecialchars($eventTitle); ?>">
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button class="carousel-btn carousel-next" onclick="slideCarousel('<?php echo $carouselId; ?>', 1)">&#10095;</button>
            </div>
        </section>
    <?php endforeach; ?>

    <script>
        const carouselState = {};

        function slideCarousel(id, direction) {
            const carousel = document.getElementById(id);
            const track = carousel.querySelector('.carousel-track');
            const slides = track.querySelectorAll('.carousel-slide');
            const wrapWidth = carousel.querySelector('.carousel-track-wrap').offsetWidth;

            if (!(id in carouselState)) carouselState[id] = 0;

            const maxIndex = Math.max(0, slides.length - 1);
            carouselState[id] = Math.min(maxIndex, Math.max(0, carouselState[id] + direction));

            track.style.transform = `translateX(-${carouselState[id] * wrapWidth}px)`;
        }
    </script>

<?php else: ?>
    <p>No media uploaded yet. Check back soon!</p>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>