<?php
require_once('db.php');


function login($user){
    $con = getConnection();
    $sql = "select * from users where username='{$user['username']}' and password='{$user['password']}'";
    $result = mysqli_query($con, $sql);   
    
    if(mysqli_num_rows($result) == 1){
        $row = mysqli_fetch_assoc($result);
        return $row;
    }else{
        return false;
    }
}


function getReports($user_id){
    $con = getConnection();
    $sql = "select * from medical_reports where user_id='$user_id'";
    $result = mysqli_query($con, $sql);
    
    $reports = [];
    while($row = mysqli_fetch_assoc($result)){
        array_push($reports, $row);
    }
    
    return $reports;
}


function addReport($report){
    $con = getConnection();
    $sql = "insert into medical_reports values(null, '{$report['user_id']}', '{$report['report_name']}', '{$report['report_date']}', '{$report['report_type']}', '{$report['doctor_lab']}', '{$report['status']}', null, null)";
    
    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
}


function getFamilyMembers($user_id){
    $con = getConnection();
    $sql = "select * from family_members where user_id='$user_id'";
    $result = mysqli_query($con, $sql);
    
    $members = [];
    while($row = mysqli_fetch_assoc($result)){
        array_push($members, $row);
    }
    
    return $members;
}


function addFamilyMember($member){
    $con = getConnection();
    $sql = "insert into family_members values(null, '{$member['user_id']}', '{$member['name']}', '{$member['relationship']}', '{$member['age']}', '{$member['blood_group']}', null)";
    
    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
}


function getAppointments($user_id){
    $con = getConnection();
    $sql = "select * from appointments where user_id='$user_id'";
    $result = mysqli_query($con, $sql);
    
    $appointments = [];
    while($row = mysqli_fetch_assoc($result)){
        array_push($appointments, $row);
    }
    
    return $appointments;
}


function addAppointment($appointment){
    $con = getConnection();
    $sql = "insert into appointments values(null, '{$appointment['user_id']}', '{$appointment['appointment_date']}', '{$appointment['appointment_time']}', '{$appointment['doctor_lab']}', '{$appointment['appointment_type']}', 'Scheduled', null)";
    
    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
}


function getVerificationCount($user_id){
    $con = getConnection();
    $sql = "select * from verifications where user_id='$user_id'";
    $result = mysqli_query($con, $sql);
    
    $count = mysqli_num_rows($result);
    return $count;
}


function addVerification($verification){
    $con = getConnection();
    $date = date('Y-m-d');
    $sql = "insert into verifications values(null, '{$verification['user_id']}', '$date', '{$verification['verification_type']}', 'Completed', null, null)";
    
    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
}


function getReportCount($user_id){
    $con = getConnection();
    $sql = "select * from medical_reports where user_id='$user_id'";
    $result = mysqli_query($con, $sql);
    
    $count = mysqli_num_rows($result);
    return $count;
}


function getUpcomingAppointment($user_id){
    $con = getConnection();
    $sql = "select * from appointments where user_id='$user_id' and status='Scheduled' order by appointment_date asc limit 1";
    $result = mysqli_query($con, $sql);
    
    if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);
        return $row;
    }else{
        return false;
    }
}


function getFamilyMemberCount($user_id){
    $con = getConnection();
    $sql = "select * from family_members where user_id='$user_id'";
    $result = mysqli_query($con, $sql);
    
    $count = mysqli_num_rows($result);
    return $count;
}


function getRecentActivity($user_id){
    $con = getConnection();
    $sql = "select * from activity_log where user_id='$user_id' order by created_at desc limit 3";
    $result = mysqli_query($con, $sql);
    
    $activities = [];
    while($row = mysqli_fetch_assoc($result)){
        array_push($activities, $row);
    }
    
    return $activities;
}

?>
