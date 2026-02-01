<?php
    session_start();
    require_once('../Models/counterfeitModel.php');
    
    if(!isset($_SESSION['user_id'])){
        header('location: ../Views/login.php');
        exit();
    }
    
    if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin'){
        header('location: ../Views/verify_medicine.php');
        exit();
    }
    
    if(isset($_REQUEST['id']) && isset($_REQUEST['status'])){
        $report_id = $_REQUEST['id'];
        $status = $_REQUEST['status'];
        
        // Validate status
        if(!in_array($status, ['Verified', 'Rejected'])){
            $_SESSION['error'] = "Invalid status!";
            header('location: ../Views/review_counterfeits.php');
            exit();
        }
        
        $admin_notes = "";
        if($status == 'Verified'){
            $admin_notes = "Verified as counterfeit by admin on " . date('Y-m-d H:i:s');
        }else{
            $admin_notes = "Not a counterfeit - rejected by admin on " . date('Y-m-d H:i:s');
        }
        
        $result = updateCounterfeitStatus($report_id, $status, $admin_notes);
        
        if($result){
            $_SESSION['success'] = "Report status updated to: " . $status;
            header('location: ../Views/review_counterfeits.php');
        }else{
            $_SESSION['error'] = "Failed to update report status!";
            header('location: ../Views/review_counterfeits.php');
        }
    }else{
        header('location: ../Views/review_counterfeits.php');
    }
?>
