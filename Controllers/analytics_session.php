<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once '../Models/medicineVerificationModel.php';
require_once '../Models/counterfeitModel.php';
require_once '../Models/medicineModel.php';
require_once '../Models/manufacturerModel.php';

// Get analytics data
$verificationTrends = getVerificationTrendsByMonth();
$categoryDistribution = getVerificationsByCategory();
$topMedicines = getTopVerifiedMedicines();
$manufacturerStats = getManufacturerCounterfeitRates();
$geographicDistribution = getVerificationsByCountry();
$dailyStats = getLast7DaysStats();
?>
