<?php
    session_start();
    require_once('../Models/reportModel.php');
    
    if(!isset($_SESSION['user_id'])){
        header('location: ../Views/login.php');
        exit();
    }
    
    if(isset($_POST['submit'])){
        $user_id = $_SESSION['user_id'];
        $report_name = $_REQUEST['report_name'];
        $report_type = $_REQUEST['report_type'];
        $notes = $_REQUEST['notes'];
        
        if($report_name == ""){
            echo "Report name is required!";
        }else if($report_type == ""){
            echo "Report type is required!";
        }else if(!isset($_FILES['myfile']) || $_FILES['myfile']['error'] != 0){
            echo "Please select a file to upload!";
        }else{
            
            $src = $_FILES['myfile']['tmp_name'];
            $ext = explode('.', $_FILES['myfile']['name']);
            $count = count($ext);
            $fileExt = $ext[$count-1];
            $des = "../uploads/".time().".".$fileExt;
            
            if(move_uploaded_file($src, $des)){
                
                $upload_date = date('Y-m-d H:i:s');
                
                $report = [
                    'user_id'=> $user_id,
                    'family_member_id'=> null,
                    'report_name'=> $report_name,
                    'report_type'=> $report_type,
                    'file_path'=> $des,
                    'upload_date'=> $upload_date,
                    'notes'=> $notes
                ];
                
                $result = addReport($report);
                
                if($result){
                    header('location: ../Views/upload_report.php');
                }else{
                    echo "Failed to add report!";
                }
                
            }else{
                echo "File upload error!";
            }
        }
    }else{
        header('location: ../Views/upload_report.php');
    }
?>
