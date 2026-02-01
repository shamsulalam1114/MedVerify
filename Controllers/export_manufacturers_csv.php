<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Views/login.php");
    exit();
}

require_once '../Models/manufacturerModel.php';

// Get filters
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'All';

// Get manufacturers
$manufacturers = getAllManufacturersWithStats($searchQuery, $statusFilter);

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="manufacturers_export_' . date('Y-m-d_H-i-s') . '.csv"');

// Create output stream
$output = fopen('php://output', 'w');

// Add UTF-8 BOM for Excel compatibility
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Add CSV headers
fputcsv($output, [
    'Manufacturer ID',
    'Name',
    'Country',
    'Address',
    'Website',
    'License Number',
    'License Expiry',
    'Certifications',
    'Contact Email',
    'Contact Phone',
    'Medicines Count',
    'Verification Rate (%)',
    'Status',
    'Created At'
]);

// Add data rows
foreach ($manufacturers as $manufacturer) {
    fputcsv($output, [
        $manufacturer['manufacturer_id'],
        $manufacturer['name'],
        $manufacturer['country'],
        $manufacturer['address'] ?? '',
        $manufacturer['website'] ?? '',
        $manufacturer['license_number'] ?? '',
        $manufacturer['license_expiry'] ?? '',
        $manufacturer['certifications'] ?? '',
        $manufacturer['contact_email'] ?? '',
        $manufacturer['contact_phone'] ?? '',
        $manufacturer['medicines_count'],
        $manufacturer['verification_rate'],
        $manufacturer['status'],
        $manufacturer['created_at']
    ]);
}

fclose($output);
exit();
?>
