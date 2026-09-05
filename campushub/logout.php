<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';
session_unset();
session_destroy();
header("Location: " . BASE_URL . "/index.php");
exit();
?>