<?php
require_once('db.php');

function addMedicineVerification($verification){
    $con = getConnection();

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

function getUnresolvedAlertsCount(){
    $con = getConnection();
    $sql = "SELECT COUNT(*) as count FROM verification_alerts WHERE is_resolved = 'No'";
    $result = mysqli_query($con, $sql);

    if($row = mysqli_fetch_assoc($result)){
        return $row['count'];
    }
    return 0;
}

function deleteVerification($id){
    $con = getConnection();
    $sql = "DELETE FROM medicine_verifications WHERE verification_id='$id'";

    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
}

function getVerificationTrendsByMonth(){
    $con = getConnection();
    $sql = "SELECT 
                DATE_FORMAT(verified_at, '%Y-%m') as month,
                COUNT(*) as count,
                SUM(CASE WHEN verification_result = 'Genuine' THEN 1 ELSE 0 END) as genuine,
                SUM(CASE WHEN verification_result = 'Suspicious' THEN 1 ELSE 0 END) as suspicious,
                SUM(CASE WHEN verification_result = 'Counterfeit' THEN 1 ELSE 0 END) as counterfeit
            FROM medicine_verifications
            WHERE verified_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(verified_at, '%Y-%m')
            ORDER BY month ASC";
    $result = mysqli_query($con, $sql);

    $trends = [];
    while($row = mysqli_fetch_assoc($result)){
        array_push($trends, $row);
    }
    return $trends;
}

function getVerificationsByCategory(){
    $con = getConnection();
    $sql = "SELECT 
                m.category,
                COUNT(*) as count
            FROM medicine_verifications mv
            JOIN medicines m ON mv.medicine_id = m.medicine_id
            GROUP BY m.category
            ORDER BY count DESC
            LIMIT 10";
    $result = mysqli_query($con, $sql);

    $categories = [];
    while($row = mysqli_fetch_assoc($result)){
        array_push($categories, $row);
    }
    return $categories;
}

function getTopVerifiedMedicines(){
    $con = getConnection();
    $sql = "SELECT 
                m.medicine_name,
                COUNT(*) as count
            FROM medicine_verifications mv
            JOIN medicines m ON mv.medicine_id = m.medicine_id
            GROUP BY m.medicine_id
            ORDER BY count DESC
            LIMIT 10";
    $result = mysqli_query($con, $sql);

    $medicines = [];
    while($row = mysqli_fetch_assoc($result)){
        array_push($medicines, $row);
    }
    return $medicines;
}

function getManufacturerCounterfeitRates(){
    $con = getConnection();
    $sql = "SELECT 
                mf.manufacturer_name,
                COUNT(*) as total_verifications,
                SUM(CASE WHEN mv.verification_result = 'Counterfeit' THEN 1 ELSE 0 END) as counterfeit_count,
                ROUND((SUM(CASE WHEN mv.verification_result = 'Counterfeit' THEN 1 ELSE 0 END) * 100.0) / COUNT(*), 2) as counterfeit_rate
            FROM medicine_verifications mv
            JOIN medicines m ON mv.medicine_id = m.medicine_id
            JOIN manufacturers mf ON m.manufacturer_id = mf.manufacturer_id
            GROUP BY mf.manufacturer_id
            HAVING total_verifications >= 5
            ORDER BY counterfeit_rate DESC
            LIMIT 10";
    $result = mysqli_query($con, $sql);

    $manufacturers = [];
    while($row = mysqli_fetch_assoc($result)){
        array_push($manufacturers, $row);
    }
    return $manufacturers;
}

function getVerificationsByCountry(){
    $con = getConnection();
    $sql = "SELECT 
                mf.country,
                COUNT(*) as count
            FROM medicine_verifications mv
            JOIN medicines m ON mv.medicine_id = m.medicine_id
            JOIN manufacturers mf ON m.manufacturer_id = mf.manufacturer_id
            GROUP BY mf.country
            ORDER BY count DESC
            LIMIT 10";
    $result = mysqli_query($con, $sql);

    $countries = [];
    while($row = mysqli_fetch_assoc($result)){
        array_push($countries, $row);
    }
    return $countries;
}

function getLast7DaysStats(){
    $con = getConnection();
    $sql = "SELECT 
                DATE(verified_at) as date,
                COUNT(*) as count,
                SUM(CASE WHEN verification_result = 'Genuine' THEN 1 ELSE 0 END) as genuine,
                SUM(CASE WHEN verification_result = 'Suspicious' THEN 1 ELSE 0 END) as suspicious,
                SUM(CASE WHEN verification_result = 'Counterfeit' THEN 1 ELSE 0 END) as counterfeit
            FROM medicine_verifications
            WHERE verified_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            GROUP BY DATE(verified_at)
            ORDER BY date ASC";
    $result = mysqli_query($con, $sql);

    $stats = [];
    while($row = mysqli_fetch_assoc($result)){
        array_push($stats, $row);
    }
    return $stats;
}

?>
