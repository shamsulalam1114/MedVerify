<?php
    session_start();
    require_once('../Models/medicineVerificationModel.php');
    
    if(!isset($_SESSION['user_id'])){
        header('location: ../Views/login.php');
        exit();
    }
    
    if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin'){
        header('location: ../Views/verify_medicine.php');
        exit();
    }
    
    if(isset($_REQUEST['id'])){
        $id = $_REQUEST['id'];
        
        $result = deleteVerification($id);
        
        if($result){
            $_SESSION['success'] = "Verification record deleted successfully!";
            header('location: ../Views/verification_history.php');
        }else{
            $_SESSION['error'] = "Failed to delete verification record!";
            header('location: ../Views/verification_history.php');
        }
    }else{
        header('location: ../Views/verification_history.php');
    }
?>
