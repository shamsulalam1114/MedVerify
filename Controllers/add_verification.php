<?php
    session_start();
    require_once('../Models/verificationModel.php');
    
    if(!isset($_SESSION['user_id'])){
        header('location: ../Views/login.php');
        exit();
    }
    
    if(isset($_POST['submit'])){
        $user_id = $_SESSION['user_id'];
        $verification_type = $_REQUEST['verification_type'];
        
        if($verification_type == ""){
            echo "Verification type is required!";
        }else{
            
            $verification = [
                'user_id'=> $user_id,
                'verification_type'=> $verification_type
            ];
            
            $result = addVerification($verification);
            
            if($result){
                header('location: ../Views/dashboard.php');
            }else{
                echo "Failed to add verification!";
            }
        }
    }else{
        header('location: ../Views/dashboard.php');
    }
?>
