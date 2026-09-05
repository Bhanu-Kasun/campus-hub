<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . " - CampusHub" : "CampusHub"; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>

<header class="site-header">
    <div class="logo">CampusHub</div>
    <nav>
        <ul class="nav-links">
            <li><a href="<?php echo BASE_URL; ?>/index.php">Home</a></li>
            <li><a href="<?php echo BASE_URL; ?>/events.php">Events</a></li>            
            <li><a href="<?php echo BASE_URL; ?>/announcements.php">Announcements</a></li> 
            <li><a href="<?php echo BASE_URL; ?>/gallery.php">Gallery</a></li> 
                        
            <?php if (isStudentLoggedIn()): ?>
                <li><a href="<?php echo BASE_URL; ?>/student/profile.php">My Profile</a></li>
                <li><a href="<?php echo BASE_URL; ?>/logout.php">Logout</a></li>                
            <?php else: ?>
                <li><a href="<?php echo BASE_URL; ?>/login.php">Login</a></li>
                <li><a href="<?php echo BASE_URL; ?>/register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main class="site-content">
