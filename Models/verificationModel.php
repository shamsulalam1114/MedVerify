<?php
require_once('db.php');


function getVerifications($user_id){
    $con = getConnection();
    $sql = "select * from verifications where user_id='$user_id'";
    $result = mysqli_query($con, $sql);
    
    $verifications = [];
    while($row = mysqli_fetch_assoc($result)){
        array_push($verifications, $row);
    }
    
    return $verifications;
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

function deleteVerification($id){
    $con = getConnection();
    $sql = "delete from verifications where verification_id='$id'";
    
    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
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
