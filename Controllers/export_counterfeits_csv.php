<?php
    session_start();
    require_once('../Models/counterfeitModel.php');
    
    if(!isset($_SESSION['user_id'])){
        header('location: ../Views/login.php');
        exit();
    }
    
    if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin'){
        header('location: ../Views/verify_medicine.php');
        exit();
    }
    
    // Get filter parameter
    $filter_status = isset($_GET['filter_status']) ? $_GET['filter_status'] : 'All';
    
    // Get all reports
    $allReports = getAllCounterfeitReports();
    
    // Apply filter
    $reports = [];
    foreach($allReports as $report){
        if($filter_status == 'All' || $report['status'] == $filter_status){
            array_push($reports, $report);
        }
    }
    
    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=counterfeit_reports_' . date('Y-m-d') . '.csv');
    
    // Create output stream
    $output = fopen('php://output', 'w');
    
    // Add BOM for UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Add headers
    fputcsv($output, [
        'Report ID',
        'Reported Date',
        'Reported By',
        'User Email',
        'Medicine Name',
        'Manufacturer',
        'Barcode',
        'Batch Number',
        'Purchase Location',
        'Purchase Date',
        'Issue Description',
        'Status',
        'Reviewed Date',
        'Admin Notes'
    ]);
    
    // Add data rows
    foreach($reports as $report){
        fputcsv($output, [
            $report['report_id'],
            date('Y-m-d H:i:s', strtotime($report['reported_date'])),
            $report['username'],
            $report['email'],
            $report['medicine_name'] ? $report['medicine_name'] : 'Unknown Medicine',
            $report['manufacturer_name'] ? $report['manufacturer_name'] : 'N/A',
            $report['barcode_scanned'],
            $report['batch_number'],
            $report['purchase_location'],
            $report['purchase_date'],
            $report['reported_issue'],
            $report['status'],
            $report['reviewed_date'] ? date('Y-m-d H:i:s', strtotime($report['reviewed_date'])) : 'Not Reviewed',
            $report['admin_notes'] ? $report['admin_notes'] : 'N/A'
        ]);
    }
    
    fclose($output);
    exit();
?>
