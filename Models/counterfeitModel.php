<?php
require_once('db.php');

// Add new counterfeit report
function addCounterfeitReport($report){
    $con = getConnection();
    $sql = "INSERT INTO reported_counterfeits (medicine_id, user_id, barcode_scanned, batch_number, 
            purchase_location, purchase_date, reported_issue, evidence_photo, status) 
            VALUES ('{$report['medicine_id']}', '{$report['user_id']}', '{$report['barcode_scanned']}', 
            '{$report['batch_number']}', '{$report['purchase_location']}', '{$report['purchase_date']}', 
            '{$report['reported_issue']}', '{$report['evidence_photo']}', 'Pending')";
    
    $result = mysqli_query($con, $sql);
    return $result;
}

// Get all counterfeit reports
function getAllCounterfeitReports(){
    $con = getConnection();
    $sql = "SELECT rc.*, m.medicine_name, m.manufacturer_id, mf.manufacturer_name, 
            u.username, u.email 
            FROM reported_counterfeits rc 
            LEFT JOIN medicines m ON rc.report_medicine_id = m.medicine_id 
            LEFT JOIN manufacturers mf ON m.manufacturer_id = mf.manufacturer_id 
            LEFT JOIN users u ON rc.user_id = u.user_id 
            ORDER BY rc.reported_date DESC";
    $result = mysqli_query($con, $sql);
    
    $reports = [];
    while($row = mysqli_fetch_assoc($result)){
        array_push($reports, $row);
    }
    
    return $reports;
}

// Get counterfeit report by ID
function getCounterfeitReportById($id){
    $con = getConnection();
    $sql = "SELECT rc.*, m.medicine_name, m.generic_name, m.manufacturer_id, 
            mf.manufacturer_name, u.username, u.email, u.phone 
            FROM reported_counterfeits rc 
            LEFT JOIN medicines m ON rc.report_medicine_id = m.medicine_id 
            LEFT JOIN manufacturers mf ON m.manufacturer_id = mf.manufacturer_id 
            LEFT JOIN users u ON rc.user_id = u.user_id 
            WHERE rc.report_id = '$id'";
    $result = mysqli_query($con, $sql);
    
    if(mysqli_num_rows($result) > 0){
        return mysqli_fetch_assoc($result);
    }
    return false;
}

// Get user's counterfeit reports
function getUserCounterfeitReports($user_id){
    $con = getConnection();
    $sql = "SELECT rc.*, m.medicine_name, mf.manufacturer_name 
            FROM reported_counterfeits rc 
            LEFT JOIN medicines m ON rc.report_medicine_id = m.medicine_id 
            LEFT JOIN manufacturers mf ON m.manufacturer_id = mf.manufacturer_id 
            WHERE rc.user_id = '$user_id' 
            ORDER BY rc.reported_date DESC";
    $result = mysqli_query($con, $sql);
    
    $reports = [];
    while($row = mysqli_fetch_assoc($result)){
        array_push($reports, $row);
    }
    
    return $reports;
}

// Update counterfeit report status
function updateCounterfeitStatus($report_id, $status, $admin_notes = ''){
    $con = getConnection();
    $sql = "UPDATE reported_counterfeits SET 
            status = '$status', 
            admin_notes = '$admin_notes', 
            reviewed_date = NOW() 
            WHERE report_id = '$report_id'";
    
    $result = mysqli_query($con, $sql);
    return $result;
}

// Get counterfeit statistics
function getCounterfeitStats(){
    $con = getConnection();
    
    $stats = [
        'total_reports' => 0,
        'pending_reports' => 0,
        'verified_reports' => 0,
        'rejected_reports' => 0
    ];
    
    $sql = "SELECT COUNT(*) as total FROM reported_counterfeits";
    $result = mysqli_query($con, $sql);
    if($row = mysqli_fetch_assoc($result)){
        $stats['total_reports'] = $row['total'];
    }
    
    $sql = "SELECT COUNT(*) as pending FROM reported_counterfeits WHERE status = 'Pending'";
    $result = mysqli_query($con, $sql);
    if($row = mysqli_fetch_assoc($result)){
        $stats['pending_reports'] = $row['pending'];
    }
    
    $sql = "SELECT COUNT(*) as verified FROM reported_counterfeits WHERE status = 'Verified'";
    $result = mysqli_query($con, $sql);
    if($row = mysqli_fetch_assoc($result)){
        $stats['verified_reports'] = $row['verified'];
    }
    
    $sql = "SELECT COUNT(*) as rejected FROM reported_counterfeits WHERE status = 'Rejected'";
    $result = mysqli_query($con, $sql);
    if($row = mysqli_fetch_assoc($result)){
        $stats['rejected_reports'] = $row['rejected'];
    }
    
    return $stats;
}

// Get pending reports count
function getPendingCounterfeitCount(){
    $con = getConnection();
    $sql = "SELECT COUNT(*) as count FROM reported_counterfeits WHERE status = 'Pending'";
    $result = mysqli_query($con, $sql);
    
    if($row = mysqli_fetch_assoc($result)){
        return $row['count'];
    }
    return 0;
}

// Delete counterfeit report
function deleteCounterfeitReport($id){
    $con = getConnection();
    $sql = "DELETE FROM reported_counterfeits WHERE report_id = '$id'";
    $result = mysqli_query($con, $sql);
    return $result;
}

// Get recent counterfeit reports
function getRecentCounterfeitReports($limit = 10){
    $con = getConnection();
    $sql = "SELECT rc.*, m.medicine_name, mf.manufacturer_name, u.username 
            FROM reported_counterfeits rc 
            LEFT JOIN medicines m ON rc.report_medicine_id = m.medicine_id 
            LEFT JOIN manufacturers mf ON m.manufacturer_id = mf.manufacturer_id 
            LEFT JOIN users u ON rc.user_id = u.user_id 
            ORDER BY rc.reported_date DESC 
            LIMIT $limit";
    $result = mysqli_query($con, $sql);
    
    $reports = [];
    while($row = mysqli_fetch_assoc($result)){
        array_push($reports, $row);
    }
    
    return $reports;
}

?>
