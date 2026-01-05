<?php
    session_start();
    require_once('../Models/appointmentModel.php');
    
    if(isset($_POST['submit'])){
        $user_id = $_SESSION['user_id'];
        $appointment_date = $_REQUEST['appointment_date'];
        $appointment_time = $_REQUEST['appointment_time'];
        $doctor_lab = $_REQUEST['doctor_lab'];
        $appointment_type = $_REQUEST['appointment_type'];
        
        if($appointment_date == "" || $doctor_lab == "" || $appointment_type == ""){
            echo "All fields are required!";
        }else{
            
            $appointment = [
                'user_id'=> $user_id,
                'appointment_date'=> $appointment_date,
                'appointment_time'=> $appointment_time,
                'doctor_lab'=> $doctor_lab,
                'appointment_type'=> $appointment_type
            ];
            
            $result = addAppointment($appointment);
            
            if($result){
                header('location: ../Views/calendar.php');
            }else{
                echo "Failed to add appointment!";
            }
        }
    }else{
        header('location: ../Views/calendar.php');
    }
?>
