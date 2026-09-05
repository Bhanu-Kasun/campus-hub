<?php
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';
requireStudentLogin();

$pageTitle = "My Profile";
$studentId = $_SESSION['student_id'];
$message = '';
$messageType = '';

// Handle profile update (Update operation) + optional photo upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName      = cleanInput($conn, $_POST['full_name'] ?? '');
    $contact       = cleanInput($conn, $_POST['contact_no'] ?? '');
    $institutionId = isset($_POST['institution_id']) && $_POST['institution_id'] !== '' ? (int)$_POST['institution_id'] : null;

    $photoClause = '';
    if (!empty($_FILES['profile_photo']['name'])) {
        $upload = handleFileUpload('profile_photo', '../uploads/profiles');
        if ($upload['success']) {
            $photoClause = ", profile_photo = '" . $upload['filename'] . "'";
        } else {
            $message = $upload['error'];
            $messageType = 'error-msg';
        }
    }

    if (empty($message)) {
        $institutionClause = $institutionId !== null ? ", institution_id = $institutionId" : ", institution_id = NULL";
        $updateQuery = "UPDATE students SET full_name = '$fullName', contact_no = '$contact' $institutionClause $photoClause WHERE student_id = $studentId";
        if (mysqli_query($conn, $updateQuery)) {
            // Update club memberships: clear existing, re-insert selected
            mysqli_query($conn, "DELETE FROM student_clubs WHERE student_id = $studentId");
            if (!empty($_POST['club_ids']) && is_array($_POST['club_ids'])) {
                $values = [];
                foreach ($_POST['club_ids'] as $clubId) {
                    $clubId = (int)$clubId;
                    if ($clubId > 0) {
                        $values[] = "($studentId, $clubId)";
                    }
                }
                if (!empty($values)) {
                    $insertClubsQuery = "INSERT INTO student_clubs (student_id, club_id) VALUES " . implode(', ', $values);
                    mysqli_query($conn, $insertClubsQuery);
                }
            }

            $_SESSION['student_name'] = $fullName;
            $message = "Profile updated successfully.";
            $messageType = "success-msg";
        } else {
            $message = "Update failed. Please try again.";
            $messageType = "error-msg";
        }
    }
}

$query = "SELECT * FROM students WHERE student_id = $studentId";
$result = mysqli_query($conn, $query);
$student = mysqli_fetch_assoc($result);

// Fetch institutions for the dropdown
$institutions = mysqli_query($conn, "SELECT institution_id, institution_name FROM institutions ORDER BY institution_name ASC");

// Fetch all clubs, and this student's current memberships
$clubs = mysqli_query($conn, "SELECT club_id, club_name FROM clubs ORDER BY club_name ASC");

$myClubIds = [];
$myClubsResult = mysqli_query($conn, "SELECT club_id FROM student_clubs WHERE student_id = $studentId");
while ($row = mysqli_fetch_assoc($myClubsResult)) {
    $myClubIds[] = (int)$row['club_id'];
}

include '../includes/header.php';
?>

<h1>My Profile</h1>

<?php if ($message): ?>
    <div class="<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<form class="form-box" method="POST" action="profile.php" enctype="multipart/form-data">

    <div style="text-align:center;">
        <img id="photo-preview"
             src="<?php echo !empty($student['profile_photo']) ? BASE_URL . '/uploads/profiles/' . htmlspecialchars($student['profile_photo']) : BASE_URL . '/assets/images/default-avatar.png'; ?>"
             style="width:110px; height:110px; border-radius:50%; object-fit:cover; border:3px solid #1f3a5f;">
    </div>

    <label for="full_name">Full Name</label>
    <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($student['full_name']); ?>" required>

    <label for="email">Email (read-only)</label>
    <input type="email" value="<?php echo htmlspecialchars($student['email']); ?>" disabled>

    <label for="contact_no">Contact Number</label>
    <input type="text" id="contact_no" name="contact_no" value="<?php echo htmlspecialchars($student['contact_no']); ?>">

    <label for="institution_id">Institution</label>
    <select id="institution_id" name="institution_id">
        <option value="">-- Select Institution --</option>
        <?php while ($inst = mysqli_fetch_assoc($institutions)): ?>
            <option value="<?php echo $inst['institution_id']; ?>" <?php echo ((int)$student['institution_id'] === (int)$inst['institution_id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($inst['institution_name']); ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label for="profile_photo">Update Profile Photo</label>
    <input type="file" id="profile_photo" name="profile_photo" accept="image/*" onchange="previewPhoto(this)">

    <label>Clubs I'm In</label>
    <div style="display:flex; flex-direction:column; gap:0.5rem; margin-top:0.4rem;">
        <?php if (mysqli_num_rows($clubs) > 0): ?>
            <?php while ($club = mysqli_fetch_assoc($clubs)): ?>
                <label style="font-weight:normal; display:flex; align-items:center; gap:0.5rem; margin-top:0;">
                    <input type="checkbox" name="club_ids[]" value="<?php echo $club['club_id']; ?>"
                        <?php echo in_array((int)$club['club_id'], $myClubIds) ? 'checked' : ''; ?>
                        style="width:auto; margin-top:0;">
                    <?php echo htmlspecialchars($club['club_name']); ?>
                </label>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="color:#888; font-size:0.9rem;">No clubs available yet.</p>
        <?php endif; ?>
    </div>

    <button type="submit" class="btn" style="margin-top:1.2rem;">Save Changes</button>
</form>

<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('photo-preview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include '../includes/footer.php'; ?>