<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Views/login.php");
    exit();
}

require_once '../Models/manufacturerModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    $manufacturerId = intval($_POST['manufacturer_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $licenseNumber = trim($_POST['license_number'] ?? '');
    $status = $_POST['status'] ?? 'Active';

    if ($manufacturerId === 0) {
        header("Location: ../Views/manage_manufacturers.php?error=Invalid manufacturer ID");
        exit();
    }

    if (empty($name) || empty($country) || empty($licenseNumber)) {
        header("Location: ../Views/edit_manufacturer.php?id=$manufacturerId&error=All required fields must be filled");
        exit();
    }

    // Validate name length
    if (strlen($name) < 2) {
        header("Location: ../Views/edit_manufacturer.php?id=$manufacturerId&error=Manufacturer name must be at least 2 characters");
        exit();
    }

    // Check for duplicate license number (excluding current manufacturer)
    if (manufacturerLicenseExistsExcept($licenseNumber, $manufacturerId)) {
        header("Location: ../Views/edit_manufacturer.php?id=$manufacturerId&error=License number already exists");
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
        header("Location: ../Views/edit_manufacturer.php?id=$manufacturerId&error=Invalid email address");
        exit();
    }

    // Validate website if provided
    if (!empty($website) && !filter_var($website, FILTER_VALIDATE_URL)) {
        header("Location: ../Views/edit_manufacturer.php?id=$manufacturerId&error=Invalid website URL");
        exit();
    }

    // Prepare data for update
    $manufacturerData = [
        'manufacturer_id' => $manufacturerId,
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

    // Update manufacturer
    if (updateManufacturer($manufacturerData)) {
        header("Location: ../Views/manage_manufacturers.php?success=Manufacturer updated successfully");
        exit();
    } else {
        header("Location: ../Views/edit_manufacturer.php?id=$manufacturerId&error=Failed to update manufacturer. Please try again.");
        exit();
    }
} else {
    header("Location: ../Views/manage_manufacturers.php");
    exit();
}
?>
