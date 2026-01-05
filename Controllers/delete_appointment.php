<?php
    session_start();
    require_once('../Models/appointmentModel.php');
    
    if(isset($_REQUEST['id'])){
        $id = $_REQUEST['id'];
        
        $result = deleteAppointment($id);
        
        if($result){
            header('location: ../Views/calendar.php');
        }else{
            echo "Failed to delete appointment!";
        }
    }else{
        header('location: ../Views/calendar.php');
    }
?>
