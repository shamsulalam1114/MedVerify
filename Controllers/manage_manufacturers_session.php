<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once '../Models/manufacturerModel.php';

// Get search query
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'All';

// Get manufacturers
$manufacturers = getAllManufacturersWithStats($searchQuery, $statusFilter);

// Get statistics
$stats = getManufacturerStatistics();
?>
