<?php
// includes/functions.php
// Reusable PHP functions used across CampusHub

session_start();

// ---- Auth helpers ----
function isStudentLoggedIn() {
    return isset($_SESSION['student_id']);
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function requireStudentLogin() {
    if (!isStudentLoggedIn()) {
        header("Location: " . BASE_URL . "/login.php");
        exit();
    }
}

function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header("Location: " . BASE_URL . "/admin/login.php");
        exit();
    }
}

// ---- Input validation / sanitisation ----
function cleanInput($conn, $data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}

function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// ---- File upload helper ----
// Returns [success => bool, filename => string|null, error => string|null]
function handleFileUpload($fileInputName, $destinationDir, $allowedTypes = ['jpg', 'jpeg', 'png', 'gif']) {
    if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'filename' => null, 'error' => 'No file selected.'];
    }

    $file = $_FILES[$fileInputName];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'filename' => null, 'error' => 'Upload error occurred.'];
    }

    $maxSize = 2 * 1024 * 1024; // 2MB
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'filename' => null, 'error' => 'File exceeds 2MB limit.'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedTypes)) {
        return ['success' => false, 'filename' => null, 'error' => 'Invalid file type. Allowed: ' . implode(', ', $allowedTypes)];
    }

    $newFileName = uniqid('ch_', true) . '.' . $ext;
    $destination = rtrim($destinationDir, '/') . '/' . $newFileName;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => true, 'filename' => $newFileName, 'error' => null];
    }

    return ['success' => false, 'filename' => null, 'error' => 'Failed to save uploaded file.'];
}

// ---- Formatting helper ----
function formatEventDate($date) {
    return date("d M Y", strtotime($date));
}
?>
