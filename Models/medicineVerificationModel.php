<?php
require_once('db.php');

// Add new medicine verification
function addMedicineVerification($verification){
    $con = getConnection();
    
    // Prepare NULL values properly
    $medicine_id = $verification['medicine_id'] ? "'{$verification['medicine_id']}'" : "NULL";
    $confidence_score = isset($verification['confidence_score']) && $verification['confidence_score'] !== '' ? "'{$verification['confidence_score']}'" : "NULL";
    $image_uploaded = isset($verification['image_uploaded']) && $verification['image_uploaded'] !== '' ? "'{$verification['image_uploaded']}'" : "NULL";
    $ip_address = isset($verification['ip_address']) ? "'{$verification['ip_address']}'" : "NULL";
    $location = isset($verification['location']) && $verification['location'] !== '' ? "'{$verification['location']}'" : "NULL";
    
    $sql = "INSERT INTO medicine_verifications 
            (user_id, medicine_id, barcode_scanned, batch_number_entered, verification_method, verification_result, confidence_score, expiry_check, manufacturer_match, batch_match, image_uploaded, verification_notes, ip_address, location) 
            VALUES 
            ('{$verification['user_id']}', $medicine_id, '{$verification['barcode_scanned']}', '{$verification['batch_number_entered']}', '{$verification['verification_method']}', '{$verification['verification_result']}', $confidence_score, '{$verification['expiry_check']}', '{$verification['manufacturer_match']}', '{$verification['batch_match']}', $image_uploaded, '{$verification['verification_notes']}', $ip_address, $location)";
    
    if(mysqli_query($con, $sql)){
        return mysqli_insert_id($con);
    }else{
        return false;
    }
}

// Get all verifications for a user
function getUserVerifications($user_id){
    $con = getConnection();
    $sql = "SELECT mv.*, m.medicine_name, m.generic_name, mf.manufacturer_name 
            FROM medicine_verifications mv 
            LEFT JOIN medicines m ON mv.medicine_id = m.medicine_id 
            LEFT JOIN manufacturers mf ON m.manufacturer_id = mf.manufacturer_id 
            WHERE mv.user_id = '$user_id' 
            ORDER BY mv.verified_at DESC";
    $result = mysqli_query($con, $sql);
    
    $verifications = [];
    while($row = mysqli_fetch_assoc($result)){
        array_push($verifications, $row);
    }
    
    return $verifications;
}

// Get all verifications (Admin)
function getAllVerifications(){
    $con = getConnection();
    $sql = "SELECT mv.*, u.username, u.full_name, m.medicine_name, m.generic_name, mf.manufacturer_name 
            FROM medicine_verifications mv 
            LEFT JOIN users u ON mv.user_id = u.user_id 
            LEFT JOIN medicines m ON mv.medicine_id = m.medicine_id 
            LEFT JOIN manufacturers mf ON m.manufacturer_id = mf.manufacturer_id 
            ORDER BY mv.verified_at DESC";
    $result = mysqli_query($con, $sql);
    
    $verifications = [];
    while($row = mysqli_fetch_assoc($result)){
        array_push($verifications, $row);
    }
    
    return $verifications;
}

// Get verification by ID
function getVerificationById($id){
    $con = getConnection();
    $sql = "SELECT mv.*, m.medicine_name, m.generic_name, mf.manufacturer_name, u.username 
            FROM medicine_verifications mv 
            LEFT JOIN medicines m ON mv.medicine_id = m.medicine_id 
            LEFT JOIN manufacturers mf ON m.manufacturer_id = mf.manufacturer_id 
            LEFT JOIN users u ON mv.user_id = u.user_id 
            WHERE mv.verification_id = '$id'";
    $result = mysqli_query($con, $sql);
    
    if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);
        return $row;
    }else{
        return false;
    }
}

// Get verification statistics for user
function getUserVerificationStats($user_id){
    $con = getConnection();
    $sql = "SELECT 
            COUNT(*) as total_verifications,
            SUM(CASE WHEN verification_result = 'Genuine' THEN 1 ELSE 0 END) as genuine_count,
            SUM(CASE WHEN verification_result = 'Counterfeit' THEN 1 ELSE 0 END) as counterfeit_count,
            SUM(CASE WHEN verification_result = 'Suspicious' THEN 1 ELSE 0 END) as suspicious_count,
            SUM(CASE WHEN verification_result = 'Expired' THEN 1 ELSE 0 END) as expired_count,
            SUM(CASE WHEN verification_result = 'Not Found' THEN 1 ELSE 0 END) as not_found_count
            FROM medicine_verifications 
            WHERE user_id = '$user_id'";
    $result = mysqli_query($con, $sql);
    
    if($row = mysqli_fetch_assoc($result)){
        return $row;
    }
    return false;
}

// Get overall verification statistics (Admin)
function getOverallVerificationStats(){
    $con = getConnection();
    $sql = "SELECT 
            COUNT(*) as total_verifications,
            SUM(CASE WHEN verification_result = 'Genuine' THEN 1 ELSE 0 END) as genuine_count,
            SUM(CASE WHEN verification_result = 'Counterfeit' THEN 1 ELSE 0 END) as counterfeit_count,
            SUM(CASE WHEN verification_result = 'Suspicious' THEN 1 ELSE 0 END) as suspicious_count,
            SUM(CASE WHEN verification_result = 'Expired' THEN 1 ELSE 0 END) as expired_count,
            SUM(CASE WHEN verification_result = 'Not Found' THEN 1 ELSE 0 END) as not_found_count
            FROM medicine_verifications";
    $result = mysqli_query($con, $sql);
    
    if($row = mysqli_fetch_assoc($result)){
        return $row;
    }
    return false;
}

// Get today's verification count
function getTodayVerificationCount($user_id = null){
    $con = getConnection();
    $today = date('Y-m-d');
    
    if($user_id){
        $sql = "SELECT COUNT(*) as count FROM medicine_verifications WHERE user_id = '$user_id' AND DATE(verified_at) = '$today'";
    }else{
        $sql = "SELECT COUNT(*) as count FROM medicine_verifications WHERE DATE(verified_at) = '$today'";
    }
    
    $result = mysqli_query($con, $sql);
    
    if($row = mysqli_fetch_assoc($result)){
        return $row['count'];
    }
    return 0;
}

// Get recent verifications (last 10)
function getRecentVerifications($limit = 10, $user_id = null){
    $con = getConnection();
    
    if($user_id){
        $sql = "SELECT mv.*, m.medicine_name, m.generic_name 
                FROM medicine_verifications mv 
                LEFT JOIN medicines m ON mv.medicine_id = m.medicine_id 
                WHERE mv.user_id = '$user_id' 
                ORDER BY mv.verified_at DESC 
                LIMIT $limit";
    }else{
        $sql = "SELECT mv.*, m.medicine_name, m.generic_name, u.username 
                FROM medicine_verifications mv 
                LEFT JOIN medicines m ON mv.medicine_id = m.medicine_id 
                LEFT JOIN users u ON mv.user_id = u.user_id 
                ORDER BY mv.verified_at DESC 
                LIMIT $limit";
    }
    
    $result = mysqli_query($con, $sql);
    
    $verifications = [];
    while($row = mysqli_fetch_assoc($result)){
        array_push($verifications, $row);
    }
    
    return $verifications;
}

// Add verification alert
function addVerificationAlert($alert){
    $con = getConnection();
    $sql = "INSERT INTO verification_alerts (verification_id, alert_type, severity, alert_message) 
            VALUES ('{$alert['verification_id']}', '{$alert['alert_type']}', '{$alert['severity']}', '{$alert['alert_message']}')";
    
    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
}

// Get unresolved alerts count
function getUnresolvedAlertsCount(){
    $con = getConnection();
    $sql = "SELECT COUNT(*) as count FROM verification_alerts WHERE is_resolved = 'No'";
    $result = mysqli_query($con, $sql);
    
    if($row = mysqli_fetch_assoc($result)){
        return $row['count'];
    }
    return 0;
}

// Delete verification
function deleteVerification($id){
    $con = getConnection();
    $sql = "DELETE FROM medicine_verifications WHERE verification_id='$id'";
    
    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
}

?>
