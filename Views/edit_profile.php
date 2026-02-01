<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../Models/userModel.php';

// Get user profile
$userId = $_SESSION['user_id'];
$user = getUserById($userId);

if (!$user) {
    header("Location: logout.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - MedVerify</title>
    <link rel="stylesheet" href="../Assets/professional.css">
</head>
<body>
    <nav>
        <div class="nav-brand">MedVerify - AI-Powered Medicine Authentication</div>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="profile.php">My Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="header">
            <h1>✏️ Edit Profile</h1>
            <button onclick="window.location.href='profile.php'" class="btn-secondary">← Back to Profile</button>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <form id="editProfileForm" method="POST" action="../Controllers/update_profile.php" onsubmit="return validateEditProfile()">
                <div class="form-section">
                    <h3>Personal Information</h3>
                    
                    <div class="form-group">
                        <label for="full_name">Full Name *</label>
                        <input type="text" id="full_name" name="full_name" required value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" maxlength="100">
                        <span id="fullNameError" class="error-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($user['email']); ?>" maxlength="100">
                        <span id="emailError" class="error-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender">
                            <option value="">Prefer not to say</option>
                            <option value="Male" <?php echo ($user['gender'] ?? '') === 'Male' ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo ($user['gender'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
                            <option value="Other" <?php echo ($user['gender'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="date_of_birth">Date of Birth</label>
                        <input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo $user['date_of_birth'] ?? ''; ?>">
                        <span id="dobError" class="error-message"></span>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Contact Information</h3>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" maxlength="20" placeholder="+1234567890">
                        <span id="phoneError" class="error-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" rows="3" maxlength="500"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Save Changes</button>
                    <button type="button" onclick="window.location.href='profile.php'" class="btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../Assets/validate_edit_profile.js"></script>
</body>
</html>
