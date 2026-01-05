<?php
    session_start();
    require_once('../Models/reportModel.php');
    
    if(!isset($_SESSION['user_id'])){
        header('location: ../Views/login.php');
        exit();
    }
    
    if(isset($_POST['submit'])){
        $report_id = $_REQUEST['report_id'];
        $user_id = $_REQUEST['user_id'];
        $report_name = $_REQUEST['report_name'];
        $report_type = $_REQUEST['report_type'];
        $file_path = $_REQUEST['file_path'];
        $upload_date = $_REQUEST['upload_date'];
        $notes = $_REQUEST['notes'];
        
        if($report_name == ""){
            echo "Report name is required!";
        }else if($report_type == ""){
            echo "Report type is required!";
        }else if($file_path == ""){
            echo "File path is required!";
        }else{
            
            $report = [
                'report_id'=> $report_id,
                'user_id'=> $user_id,
                'report_name'=> $report_name,
                'report_type'=> $report_type,
                'file_path'=> $file_path,
                'upload_date'=> $upload_date,
                'notes'=> $notes,
                'family_member_id'=> null
            ];
            
            $result = updateReport($report);
            
            if($result){
                header('location: ../Views/view_reports.php');
            }else{
                echo "Failed to update report!";
            }
        }
    }else{
        header('location: ../Views/view_reports.php');
    }
?>
