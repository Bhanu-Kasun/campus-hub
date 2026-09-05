<?php
// includes/db_connect.php
// Central database connection for CampusHub

// Base URL: works whether the project lives at the web root or in a
// subfolder like localhost/campushub/. Every internal link and asset
// path in the app should be prefixed with BASE_URL instead of a bare "/".
if (!defined('BASE_URL')) {
    define('BASE_URL', '/campushub');
}

$DB_HOST = "127.0.0.1";
$DB_USER = "root";
$DB_PASS = "Bhanu12345678@";
$DB_NAME = "campushub";
$DB_PORT = 3307;

// PHP 8.1+ mysqli throws exceptions on connection failure, so we catch it
// instead of relying on a falsy return value.
try {
    $conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);
    mysqli_set_charset($conn, "utf8mb4");
} catch (mysqli_sql_exception $e) {
    // Error handling: don't leak raw DB errors to the user
    die("<p style='color:red;'>Database connection failed. Please check your database settings and try again.</p>");
}
?>
