<?php
require_once('db.php');

// Add new counterfeit report
function addCounterfeitReport($report){
    $con = getConnection();
    $sql = "INSERT INTO reported_counterfeits (user_id, medicine_name, suspected_manufacturer, 
            barcode, batch_number, purchase_location, purchase_date, report_description, 
            evidence_image, verification_status) 
            VALUES ('{$report['user_id']}', '{$report['medicine_name']}', '{$report['suspected_manufacturer']}', 
            '{$report['barcode']}', '{$report['batch_number']}', '{$report['purchase_location']}', 
            '{$report['purchase_date']}', '{$report['report_description']}', '{$report['evidence_image']}', 'Pending')";
    
    $result = mysqli_query($con, $sql);
    return $result;
}

// Get all counterfeit reports
function getAllCounterfeitReports(){
    $con = getConnection();
    $sql = "SELECT rc.*, u.username, u.email 
            FROM reported_counterfeits rc 
            LEFT JOIN users u ON rc.user_id = u.user_id 
            ORDER BY rc.reported_at DESC";
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
    $sql = "SELECT rc.*, u.username, u.email, u.phone 
            FROM reported_counterfeits rc 
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
    $sql = "SELECT rc.* 
            FROM reported_counterfeits rc 
            WHERE rc.user_id = '$user_id' 
            ORDER BY rc.reported_at DESC";
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
            verification_status = '$status', 
            admin_notes = '$admin_notes', 
            reviewed_at = NOW() 
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
    
    $sql = "SELECT COUNT(*) as pending FROM reported_counterfeits WHERE verification_status = 'Pending'";
    $result = mysqli_query($con, $sql);
    if($row = mysqli_fetch_assoc($result)){
        $stats['pending_reports'] = $row['pending'];
    }
    
    $sql = "SELECT COUNT(*) as verified FROM reported_counterfeits WHERE verification_status = 'Verified Fake'";
    $result = mysqli_query($con, $sql);
    if($row = mysqli_fetch_assoc($result)){
        $stats['verified_reports'] = $row['verified'];
    }
    
    $sql = "SELECT COUNT(*) as rejected FROM reported_counterfeits WHERE verification_status = 'Genuine'";
    $result = mysqli_query($con, $sql);
    if($row = mysqli_fetch_assoc($result)){
        $stats['rejected_reports'] = $row['rejected'];
    }
    
    return $stats;
}

// Get pending reports count
function getPendingCounterfeitCount(){
    $con = getConnection();
    $sql = "SELECT COUNT(*) as count FROM reported_counterfeits WHERE verification_status = 'Pending'";
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
    $sql = "SELECT rc.*, u.username 
            FROM reported_counterfeits rc 
            LEFT JOIN users u ON rc.user_id = u.user_id 
            ORDER BY rc.reported_at DESC 
            LIMIT $limit";
    $result = mysqli_query($con, $sql);
    
    $reports = [];
    while($row = mysqli_fetch_assoc($result)){
        array_push($reports, $row);
    }
    
    return $reports;
}

?>
