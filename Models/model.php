<?php

$con = mysqli_connect('127.0.0.1', 'root', '', 'medverify_new');


if(!$con){
    echo "Database connection failed!";
}


function checkLogin($username, $password){
    global $con;
    
    $sql = "select * from users where username='$username' and password='$password'";
    $result = mysqli_query($con, $sql);
    
    if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);
        return $row;
    }else{
        return false;
    }
}


function getReports($user_id){
    global $con;
    
    $sql = "select * from medical_reports where user_id='$user_id'";
    $result = mysqli_query($con, $sql);
    
    $reports = [];
    while($row = mysqli_fetch_assoc($result)){
        array_push($reports, $row);
    }
    
    return $reports;
}


function addReport($user_id, $report_name, $report_date, $report_type, $doctor_lab, $status){
    global $con;
    
    $sql = "insert into medical_reports values(null, '$user_id', '$report_name', '$report_date', '$report_type', '$doctor_lab', '$status', null, null)";
    
    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
}


function getFamilyMembers($user_id){
    global $con;
    
    $sql = "select * from family_members where user_id='$user_id'";
    $result = mysqli_query($con, $sql);
    
    $members = [];
    while($row = mysqli_fetch_assoc($result)){
        array_push($members, $row);
    }
    
    return $members;
}


function addFamilyMember($user_id, $name, $relationship, $age, $blood_group){
    global $con;
    
    $sql = "insert into family_members values(null, '$user_id', '$name', '$relationship', '$age', '$blood_group', null)";
    
    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
}


function getAppointments($user_id){
    global $con;
    
    $sql = "select * from appointments where user_id='$user_id'";
    $result = mysqli_query($con, $sql);
    
    $appointments = [];
    while($row = mysqli_fetch_assoc($result)){
        array_push($appointments, $row);
    }
    
    return $appointments;
}


function addAppointment($user_id, $appointment_date, $appointment_time, $doctor_lab, $appointment_type){
    global $con;
    
    $sql = "insert into appointments values(null, '$user_id', '$appointment_date', '$appointment_time', '$doctor_lab', '$appointment_type', 'Scheduled', null)";
    
    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
}


function getVerificationCount($user_id){
    global $con;
    
    $sql = "select * from verifications where user_id='$user_id'";
    $result = mysqli_query($con, $sql);
    
    $count = mysqli_num_rows($result);
    return $count;
}


function addVerification($user_id, $verification_type){
    global $con;
    
    $date = date('Y-m-d');
    $sql = "insert into verifications values(null, '$user_id', '$date', '$verification_type', 'Completed', null, null)";
    
    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
}


function getReportCount($user_id){
    global $con;
    
    $sql = "select * from medical_reports where user_id='$user_id'";
    $result = mysqli_query($con, $sql);
    
    $count = mysqli_num_rows($result);
    return $count;
}


function getUpcomingAppointment($user_id){
    global $con;
    
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
    global $con;
    
    $sql = "select * from family_members where user_id='$user_id'";
    $result = mysqli_query($con, $sql);
    
    $count = mysqli_num_rows($result);
    return $count;
}


function getRecentActivity($user_id){
    global $con;
    
    $sql = "select * from activity_log where user_id='$user_id' order by created_at desc limit 3";
    $result = mysqli_query($con, $sql);
    
    $activities = [];
    while($row = mysqli_fetch_assoc($result)){
        array_push($activities, $row);
    }
    
    return $activities;
}

?>
