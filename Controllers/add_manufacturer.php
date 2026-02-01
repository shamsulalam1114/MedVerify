<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Views/login.php");
    exit();
}

require_once '../Models/manufacturerModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    $name = trim($_POST['name'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $licenseNumber = trim($_POST['license_number'] ?? '');
    $status = $_POST['status'] ?? 'Active';

    if (empty($name) || empty($country) || empty($licenseNumber)) {
        header("Location: ../Views/add_manufacturer.php?error=All required fields must be filled");
        exit();
    }

    // Validate name length
    if (strlen($name) < 2) {
        header("Location: ../Views/add_manufacturer.php?error=Manufacturer name must be at least 2 characters");
        exit();
    }

    // Check for duplicate license number
    if (manufacturerLicenseExists($licenseNumber)) {
        header("Location: ../Views/add_manufacturer.php?error=License number already exists");
        exit();
    }

    // Optional fields
    $address = trim($_POST['address'] ?? '');
    $website = trim($_POST['website'] ?? '');
    $licenseExpiry = $_POST['license_expiry'] ?? null;
    $certifications = trim($_POST['certifications'] ?? '');
    $contactEmail = trim($_POST['contact_email'] ?? '');
    $contactPhone = trim($_POST['contact_phone'] ?? '');

    // Validate email if provided
    if (!empty($contactEmail) && !filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../Views/add_manufacturer.php?error=Invalid email address");
        exit();
    }

    // Validate website if provided
    if (!empty($website) && !filter_var($website, FILTER_VALIDATE_URL)) {
        header("Location: ../Views/add_manufacturer.php?error=Invalid website URL");
        exit();
    }

    // Validate license expiry date
    if (!empty($licenseExpiry)) {
        $expiryDate = new DateTime($licenseExpiry);
        $today = new DateTime();
        if ($expiryDate < $today) {
            header("Location: ../Views/add_manufacturer.php?error=License expiry date cannot be in the past");
            exit();
        }
    }

    // Prepare data for insertion
    $manufacturerData = [
        'name' => $name,
        'country' => $country,
        'address' => $address,
        'website' => $website,
        'license_number' => $licenseNumber,
        'license_expiry' => empty($licenseExpiry) ? null : $licenseExpiry,
        'certifications' => $certifications,
        'contact_email' => $contactEmail,
        'contact_phone' => $contactPhone,
        'status' => $status
    ];

    // Add manufacturer
    if (addManufacturer($manufacturerData)) {
        header("Location: ../Views/manage_manufacturers.php?success=Manufacturer added successfully");
        exit();
    } else {
        header("Location: ../Views/add_manufacturer.php?error=Failed to add manufacturer. Please try again.");
        exit();
    }
} else {
    header("Location: ../Views/add_manufacturer.php");
    exit();
}
?>
