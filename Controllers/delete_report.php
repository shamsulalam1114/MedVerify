<?php
    session_start();
    require_once('../Models/reportModel.php');
    
    if(isset($_REQUEST['id'])){
        $id = $_REQUEST['id'];
        
        $result = deleteReport($id);
        
        if($result){
            header('location: ../Views/view_reports.php');
        }else{
            echo "Failed to delete report!";
        }
    }else{
        header('location: ../Views/view_reports.php');
    }
?>
