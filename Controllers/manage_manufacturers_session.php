<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once '../Models/manufacturerModel.php';

$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'All';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 20;
$offset = ($page - 1) * $records_per_page;

$all_manufacturers = getAllManufacturersWithStats($searchQuery, $statusFilter);

$total_records = count($all_manufacturers);
$total_pages = ceil($total_records / $records_per_page);
$manufacturers = array_slice($all_manufacturers, $offset, $records_per_page);

// Get statistics
$stats = getManufacturerStatistics();
?>
