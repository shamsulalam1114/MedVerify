<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Views/login.php");
    exit();
}

require_once '../Models/userModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    
    // Validate inputs
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $dateOfBirth = $_POST['date_of_birth'] ?? null;
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    // Validate required fields
    if (empty($fullName) || empty($email)) {
        header("Location: ../Views/edit_profile.php?error=Full name and email are required");
        exit();
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../Views/edit_profile.php?error=Invalid email address");
        exit();
    }

    // Check if email already exists (excluding current user)
    if (emailExistsExcept($email, $userId)) {
        header("Location: ../Views/edit_profile.php?error=Email already in use by another account");
        exit();
    }

    // Validate date of birth (must be in the past)
    if (!empty($dateOfBirth)) {
        $dob = new DateTime($dateOfBirth);
        $today = new DateTime();
        if ($dob > $today) {
            header("Location: ../Views/edit_profile.php?error=Date of birth cannot be in the future");
            exit();
        }
    }

    // Prepare data for update
    $userData = [
        'user_id' => $userId,
        'full_name' => $fullName,
        'email' => $email,
        'gender' => $gender,
        'date_of_birth' => empty($dateOfBirth) ? null : $dateOfBirth,
        'phone' => $phone,
        'address' => $address
    ];

    // Update profile
    if (updateUserProfile($userData)) {
        // Update session data
        $_SESSION['username'] = $fullName;
        header("Location: ../Views/profile.php?success=Profile updated successfully");
        exit();
    } else {
        header("Location: ../Views/edit_profile.php?error=Failed to update profile. Please try again.");
        exit();
    }
} else {
    header("Location: ../Views/edit_profile.php");
    exit();
}
?>
