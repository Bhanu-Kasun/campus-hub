<?php
// db_test.php — quick standalone check that the DB connection works.
// Delete before final submission.

require_once 'includes/db_connect.php';

if ($conn) {
    $message = "Successfully connected to the database.";
    $messageType = "success-msg";
} else {
    $message = "Database connection failed.";
    $messageType = "error-msg";
}
?>

<h2>Database Connection Test</h2>
<p class="<?php echo $messageType; ?>"><?php echo $message; ?></p>