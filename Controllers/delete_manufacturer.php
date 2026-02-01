<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Views/login.php");
    exit();
}

require_once '../Models/manufacturerModel.php';

$manufacturerId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($manufacturerId === 0) {
    header("Location: ../Views/manage_manufacturers.php?error=Invalid manufacturer ID");
    exit();
}

// Check if manufacturer has associated medicines
$medicineCount = getManufacturerMedicineCount($manufacturerId);

if ($medicineCount > 0) {
    header("Location: ../Views/manage_manufacturers.php?error=Cannot delete manufacturer with $medicineCount associated medicines. Please reassign or delete medicines first.");
    exit();
}

// Delete manufacturer
if (deleteManufacturer($manufacturerId)) {
    header("Location: ../Views/manage_manufacturers.php?success=Manufacturer deleted successfully");
    exit();
} else {
    header("Location: ../Views/manage_manufacturers.php?error=Failed to delete manufacturer. Please try again.");
    exit();
}
?>
