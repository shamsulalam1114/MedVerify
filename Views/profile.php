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
    <title>My Profile - MedVerify</title>
    <link rel="stylesheet" href="../Assets/dashboard.css">
    <style>
        .profile-container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .profile-header {
            text-align: center;
            padding-bottom: 30px;
            border-bottom: 2px solid #eee;
        }
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
            font-weight: bold;
        }
        .profile-sections {
            margin-top: 30px;
        }
        .profile-section {
            margin-bottom: 30px;
        }
        .profile-section h3 {
            color: #667eea;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }
        .profile-field {
            display: flex;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .profile-field label {
            font-weight: bold;
            width: 200px;
            color: #555;
        }
        .profile-field value {
            flex: 1;
            color: #333;
        }
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <nav>
        <div class="nav-brand">MedVerify - AI-Powered Medicine Authentication</div>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="verify_medicine.php">Verify Medicine</a></li>
            <li><a href="verification_history.php">Verification History</a></li>
            <?php if($_SESSION['role'] === 'admin'): ?>
            <li><a href="manage_medicines.php">Manage Medicines</a></li>
            <li><a href="manage_manufacturers.php">Manage Manufacturers</a></li>
            <li><a href="analytics.php">Analytics</a></li>
            <li><a href="review_counterfeits.php">Review Reports</a></li>
            <?php endif; ?>
            <li><a href="report_counterfeit.php">Report Counterfeit</a></li>
            <li><a href="family_profile.php">Family Profile</a></li>
            <li><a href="calendar.php">Calendar</a></li>
            <li><a href="upload_report.php">Upload Report</a></li>
            <li><a href="view_reports.php">View Reports</a></li>
            <li><a href="profile.php" class="active">My Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
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

        <div class="profile-container">
            <div class="profile-header">
                <div class="profile-avatar">
                    <?php echo strtoupper(substr($user['full_name'] ?? $user['username'], 0, 1)); ?>
                </div>
                <h2><?php echo htmlspecialchars($user['full_name'] ?? $user['username']); ?></h2>
                <p style="color: #666;">@<?php echo htmlspecialchars($user['username']); ?></p>
                <span class="status-badge <?php echo $user['role'] === 'admin' ? 'counterfeit' : 'genuine'; ?>">
                    <?php echo ucfirst($user['role']); ?>
                </span>
            </div>

            <div class="profile-sections">
                <div class="profile-section">
                    <h3>👤 Personal Information</h3>
                    <div class="profile-field">
                        <label>Full Name:</label>
                        <value><?php echo htmlspecialchars($user['full_name'] ?? 'Not set'); ?></value>
                    </div>
                    <div class="profile-field">
                        <label>Username:</label>
                        <value><?php echo htmlspecialchars($user['username']); ?></value>
                    </div>
                    <div class="profile-field">
                        <label>Email:</label>
                        <value><?php echo htmlspecialchars($user['email']); ?></value>
                    </div>
                    <div class="profile-field">
                        <label>Gender:</label>
                        <value><?php echo htmlspecialchars($user['gender'] ?? 'Not specified'); ?></value>
                    </div>
                    <div class="profile-field">
                        <label>Date of Birth:</label>
                        <value><?php echo $user['date_of_birth'] ? date('F j, Y', strtotime($user['date_of_birth'])) : 'Not set'; ?></value>
                    </div>
                </div>

                <div class="profile-section">
                    <h3>📍 Contact Information</h3>
                    <div class="profile-field">
                        <label>Phone:</label>
                        <value><?php echo htmlspecialchars($user['phone'] ?? 'Not set'); ?></value>
                    </div>
                    <div class="profile-field">
                        <label>Address:</label>
                        <value><?php echo htmlspecialchars($user['address'] ?? 'Not set'); ?></value>
                    </div>
                </div>

                <div class="profile-section">
                    <h3>🔐 Account Security</h3>
                    <div class="profile-field">
                        <label>Account Status:</label>
                        <value><span class="status-badge genuine">Active</span></value>
                    </div>
                    <div class="profile-field">
                        <label>Member Since:</label>
                        <value><?php echo date('F j, Y', strtotime($user['created_at'])); ?></value>
                    </div>
                    <div class="profile-field">
                        <label>Last Login:</label>
                        <value><?php echo date('F j, Y g:i A', strtotime($user['last_login'] ?? $user['created_at'])); ?></value>
                    </div>
                </div>
            </div>

            <div class="action-buttons">
                <button onclick="window.location.href='edit_profile.php'" class="btn-primary">✏️ Edit Profile</button>
                <button onclick="window.location.href='change_password.php'" class="btn-secondary">🔑 Change Password</button>
                <button onclick="window.location.href='dashboard.php'" class="btn-secondary">← Back to Dashboard</button>
            </div>
        </div>
    </div>
</body>
</html>
