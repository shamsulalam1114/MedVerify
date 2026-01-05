<?php
require_once('db.php');


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

function getReportById($id){
    $con = getConnection();
    $sql = "select * from medical_reports where report_id='$id'";
    $result = mysqli_query($con, $sql);
    
    if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);
        return $row;
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

function addReport($report){
    $con = getConnection();
    $sql = "insert into medical_reports values(null, '{$report['user_id']}', '{$report['report_name']}', '{$report['report_date']}', '{$report['report_type']}', '{$report['doctor_lab']}', '{$report['status']}', null, null)";
    
    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
}

function deleteReport($id){
    $con = getConnection();
    $sql = "delete from medical_reports where report_id='$id'";
    
    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
}

function updateReport($report){
    $con = getConnection();
    $sql = "update medical_reports set report_name='{$report['report_name']}', report_date='{$report['report_date']}', report_type='{$report['report_type']}', doctor_lab='{$report['doctor_lab']}', status='{$report['status']}' where report_id='{$report['report_id']}'";
    
    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
}

?>
