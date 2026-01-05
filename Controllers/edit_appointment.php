<?php
    session_start();
    require_once('../Models/appointmentModel.php');
    
    if(isset($_POST['submit'])){
        $appointment_id = $_REQUEST['appointment_id'];
        $user_id = $_REQUEST['user_id'];
        $appointment_date = $_REQUEST['appointment_date'];
        $appointment_time = $_REQUEST['appointment_time'];
        $doctor_lab = $_REQUEST['doctor_lab'];
        $appointment_type = $_REQUEST['appointment_type'];
        $status = $_REQUEST['status'];
        
        if($appointment_date == "" || $doctor_lab == "" || $appointment_type == ""){
            echo "Date, doctor/lab and type are required!";
        }else{
            
            $appointment = [
                'appointment_id'=> $appointment_id,
                'user_id'=> $user_id,
                'appointment_date'=> $appointment_date,
                'appointment_time'=> $appointment_time,
                'doctor_lab'=> $doctor_lab,
                'appointment_type'=> $appointment_type,
                'status'=> $status
            ];
            
            $result = updateAppointment($appointment);
            
            if($result){
                header('location: ../Views/calendar.php');
            }else{
                echo "Failed to update appointment!";
            }
        }
    }else{
        header('location: ../Views/calendar.php');
    }
?>
