<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Views/login.php");
    exit();
}

require_once '../Models/userModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validate inputs
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        header("Location: ../Views/change_password.php?error=All fields are required");
        exit();
    }

    // Verify current password
    if (!verifyUserPassword($userId, $currentPassword)) {
        header("Location: ../Views/change_password.php?error=Current password is incorrect");
        exit();
    }

    // Validate new password
    if (strlen($newPassword) < 6) {
        header("Location: ../Views/change_password.php?error=New password must be at least 6 characters");
        exit();
    }

    // Check if passwords match
    if ($newPassword !== $confirmPassword) {
        header("Location: ../Views/change_password.php?error=New passwords do not match");
        exit();
    }

    // Check if new password is different from current
    if ($currentPassword === $newPassword) {
        header("Location: ../Views/change_password.php?error=New password must be different from current password");
        exit();
    }

    // Update password
    if (updateUserPassword($userId, $newPassword)) {
        header("Location: ../Views/change_password.php?success=Password updated successfully");
        exit();
    } else {
        header("Location: ../Views/change_password.php?error=Failed to update password. Please try again.");
        exit();
    }
} else {
    header("Location: ../Views/change_password.php");
    exit();
}
?>
