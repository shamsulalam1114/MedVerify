<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - MedVerify</title>
    <link rel="stylesheet" href="../Assets/dashboard.css">
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
            <h1>🔑 Change Password</h1>
            <button onclick="window.location.href='profile.php'" class="btn-secondary">← Back to Profile</button>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <form id="changePasswordForm" method="POST" action="../Controllers/update_password.php" onsubmit="return validateChangePassword()">
                <div class="form-section">
                    <h3>Password Security</h3>
                    
                    <div class="form-group">
                        <label for="current_password">Current Password *</label>
                        <input type="password" id="current_password" name="current_password" required>
                        <span id="currentPasswordError" class="error-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="new_password">New Password *</label>
                        <input type="password" id="new_password" name="new_password" required>
                        <small>Password must be at least 6 characters long</small>
                        <span id="newPasswordError" class="error-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password *</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                        <span id="confirmPasswordError" class="error-message"></span>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Update Password</button>
                    <button type="button" onclick="window.location.href='profile.php'" class="btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../Assets/validate_change_password.js"></script>
</body>
</html>
