<?php
    session_start();
    require_once('../Models/appointmentModel.php');
    
    if(!isset($_SESSION['user_id'])){
        header('location: ../Views/login.php');
        exit();
    }
    
    if(isset($_REQUEST['id'])){
        $id = $_REQUEST['id'];
        
        $result = deleteAppointment($id);
        
        if($result){
            header('location: ../Views/calendar.php');
        }else{
            echo "Failed to delete";
        }
    }else{
        header('location: ../Views/calendar.php');
    }
?>
