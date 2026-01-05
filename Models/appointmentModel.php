<?php
require_once('db.php');


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

function getAppointmentById($id){
    $con = getConnection();
    $sql = "select * from appointments where appointment_id='$id'";
    $result = mysqli_query($con, $sql);
    
    if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);
        return $row;
    }else{
        return false;
    }
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

function addAppointment($appointment){
    $con = getConnection();
    $sql = "insert into appointments values(null, '{$appointment['user_id']}', '{$appointment['appointment_date']}', '{$appointment['appointment_time']}', '{$appointment['doctor_lab']}', '{$appointment['appointment_type']}', 'Scheduled', null)";
    
    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
}

function deleteAppointment($id){
    $con = getConnection();
    $sql = "delete from appointments where appointment_id='$id'";
    
    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
}

function updateAppointment($appointment){
    $con = getConnection();
    $sql = "update appointments set appointment_date='{$appointment['appointment_date']}', appointment_time='{$appointment['appointment_time']}', doctor_lab='{$appointment['doctor_lab']}', appointment_type='{$appointment['appointment_type']}', status='{$appointment['status']}' where appointment_id='{$appointment['appointment_id']}'";
    
    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
}

function getAllAppointments(){
    $con = getConnection();
    $sql = "select appointments.*, users.username from appointments join users on appointments.user_id = users.user_id";
    $result = mysqli_query($con, $sql);
    
    $appointments = [];
    while($row = mysqli_fetch_assoc($result)){
        array_push($appointments, $row);
    }
    
    return $appointments;
}

function getAllAppointmentsCount(){
    $con = getConnection();
    $sql = "select * from appointments";
    $result = mysqli_query($con, $sql);
    
    $count = mysqli_num_rows($result);
    return $count;
}

?>
