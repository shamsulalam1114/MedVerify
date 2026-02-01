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
    
    if(isset($_REQUEST['id'])){
        $report_id = $_REQUEST['id'];
        
        $result = deleteCounterfeitReport($report_id);
        
        if($result){
            $_SESSION['success'] = "Counterfeit report deleted successfully!";
            header('location: ../Views/review_counterfeits.php');
        }else{
            $_SESSION['error'] = "Failed to delete report!";
            header('location: ../Views/review_counterfeits.php');
        }
    }else{
        header('location: ../Views/review_counterfeits.php');
    }
?>
