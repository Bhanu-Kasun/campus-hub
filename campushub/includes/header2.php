<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . " - CampusHub Admin" : "CampusHub Admin"; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>

<header class="site-header admin-header">
    <div class="admin-left">
        <span class="admin-name"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
    </div>
    <nav>
        <ul class="nav-links">
            <li><a href="<?php echo BASE_URL; ?>/admin/dashboard.php">Dashboard</a></li>
            <li><a href="<?php echo BASE_URL; ?>/admin/logout.php">Logout</a></li>
        </ul>
    </nav>
</header>

