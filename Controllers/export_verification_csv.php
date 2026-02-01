<?php
    session_start();
    require_once('../Models/medicineVerificationModel.php');
    require_once('../Models/medicineModel.php');
    
    if(!isset($_SESSION['user_id'])){
        header('location: ../Views/login.php');
        exit();
    }
    
    if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin'){
        header('location: ../Views/verify_medicine.php');
        exit();
    }
    
    // Get filter parameters
    $filter_result = isset($_GET['filter_result']) ? $_GET['filter_result'] : 'All';
    $search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
    
    // Get all verifications
    $verifications = getAllVerifications();
    
    // Apply filters
    $filtered_verifications = [];
    foreach($verifications as $verify){
        $result_match = ($filter_result == 'All' || $verify['verification_result'] == $filter_result);
        
        $search_match = true;
        if($search_query != ''){
            $search_match = (
                stripos($verify['barcode_scanned'], $search_query) !== false ||
                stripos($verify['batch_number_entered'], $search_query) !== false ||
                stripos($verify['medicine_name'], $search_query) !== false ||
                stripos($verify['username'], $search_query) !== false
            );
        }
        
        if($result_match && $search_match){
            array_push($filtered_verifications, $verify);
        }
    }
    
    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=verification_history_' . date('Y-m-d') . '.csv');
    
    // Create output stream
    $output = fopen('php://output', 'w');
    
    // Add BOM for UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Add headers
    fputcsv($output, [
        'Verification ID',
        'Date & Time',
        'User',
        'Email',
        'Medicine Name',
        'Manufacturer',
        'Barcode',
        'Batch Number',
        'Method',
        'Result',
        'Confidence Score',
        'Expiry Check',
        'Manufacturer Verified',
        'Batch Matched'
    ]);
    
    // Add data rows
    foreach($filtered_verifications as $verify){
        fputcsv($output, [
            $verify['verification_id'],
            date('Y-m-d H:i:s', strtotime($verify['verified_at'])),
            $verify['username'],
            $verify['email'],
            $verify['medicine_name'] ? $verify['medicine_name'] : 'Unknown',
            $verify['manufacturer_name'] ? $verify['manufacturer_name'] : 'N/A',
            $verify['barcode_scanned'] ? $verify['barcode_scanned'] : 'N/A',
            $verify['batch_number_entered'] ? $verify['batch_number_entered'] : 'N/A',
            $verify['verification_method'],
            $verify['verification_result'],
            $verify['confidence_score'] . '%',
            $verify['expiry_check'],
            $verify['manufacturer_verified'] == 1 ? 'Yes' : 'No',
            $verify['batch_matched'] == 1 ? 'Yes' : 'No'
        ]);
    }
    
    fclose($output);
    exit();
?>
