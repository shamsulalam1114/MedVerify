<?php
    session_start();
    require_once('../Models/medicineModel.php');
    
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
        
        $result = deleteMedicine($id);
        
        if($result){
            $_SESSION['success'] = "Medicine deleted successfully!";
            header('location: ../Views/manage_medicines.php');
        }else{
            $_SESSION['error'] = "Failed to delete medicine!";
            header('location: ../Views/manage_medicines.php');
        }
    }else{
        header('location: ../Views/manage_medicines.php');
    }
?>
