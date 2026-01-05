<?php
    session_start();
    require_once('../Models/familyModel.php');
    
    if(!isset($_SESSION['user_id'])){
        header('location: ../Views/login.php');
        exit();
    }
    
    if(isset($_REQUEST['id'])){
        $id = $_REQUEST['id'];
        
        $result = deleteFamilyMember($id);
        
        if($result){
            header('location: ../Views/family_profile.php');
        }else{
            echo "Failed to delete family member!";
        }
    }else{
        header('location: ../Views/family_profile.php');
    }
?>
