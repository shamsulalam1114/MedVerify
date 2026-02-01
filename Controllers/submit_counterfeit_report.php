<?php
    session_start();
    require_once('../Models/medicineModel.php');
    require_once('../Models/counterfeitModel.php');
    require_once('../Models/emailModel.php');
    require_once('../Models/userModel.php');
    
    if(!isset($_SESSION['user_id'])){
        header('location: ../Views/login.php');
        exit();
    }
    
    if(isset($_POST['submit'])){
        $user_id = $_SESSION['user_id'];
        $barcode = trim($_REQUEST['barcode']);
        $batch_number = trim($_REQUEST['batch_number']);
        $purchase_location = trim($_REQUEST['purchase_location']);
        $purchase_date = $_REQUEST['purchase_date'];
        $reported_issue = trim($_REQUEST['reported_issue']);
        
        // Validation
        if($barcode == ""){
            $_SESSION['error'] = "Barcode is required!";
            header('location: ../Views/report_counterfeit.php');
            exit();
        }
        
        if(strlen($barcode) < 10 || strlen($barcode) > 13){
            $_SESSION['error'] = "Barcode must be 10-13 digits!";
            header('location: ../Views/report_counterfeit.php');
            exit();
        }
        
        if($reported_issue == "" || strlen($reported_issue) < 20){
            $_SESSION['error'] = "Please provide detailed description of the issue (at least 20 characters)!";
            header('location: ../Views/report_counterfeit.php');
            exit();
        }
        
        // Handle file upload
        $evidence_photo = "";
        if(isset($_FILES['evidence_photo']) && $_FILES['evidence_photo']['error'] == 0){
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
            $file_type = $_FILES['evidence_photo']['type'];
            $file_size = $_FILES['evidence_photo']['size'];
            
            if(!in_array($file_type, $allowed_types)){
                $_SESSION['error'] = "Only JPG, PNG, GIF images are allowed!";
                header('location: ../Views/report_counterfeit.php');
                exit();
            }
            
            if($file_size > 5000000){ // 5MB
                $_SESSION['error'] = "File size must be less than 5MB!";
                header('location: ../Views/report_counterfeit.php');
                exit();
            }
            
            // Create upload directory if not exists
            $upload_dir = "../uploads/counterfeits/";
            if(!is_dir($upload_dir)){
                mkdir($upload_dir, 0777, true);
            }
            
            // Generate unique filename
            $file_extension = pathinfo($_FILES['evidence_photo']['name'], PATHINFO_EXTENSION);
            $evidence_photo = "counterfeit_" . time() . "_" . rand(1000, 9999) . "." . $file_extension;
            $upload_path = $upload_dir . $evidence_photo;
            
            if(!move_uploaded_file($_FILES['evidence_photo']['tmp_name'], $upload_path)){
                $_SESSION['error'] = "Failed to upload evidence photo!";
                header('location: ../Views/report_counterfeit.php');
                exit();
            }
        }else{
            $_SESSION['error'] = "Evidence photo is required!";
            header('location: ../Views/report_counterfeit.php');
            exit();
        }
        
        // Try to find medicine by barcode
        $medicine = getMedicineByBarcode($barcode);
        $medicine_id = $medicine ? $medicine['medicine_id'] : NULL;
        
        // Prepare report data
        $report = [
            'medicine_id' => $medicine_id,
            'user_id' => $user_id,
            'barcode_scanned' => $barcode,
            'batch_number' => $batch_number,
            'purchase_location' => $purchase_location,
            'purchase_date' => $purchase_date,
            'reported_issue' => $reported_issue,
            'evidence_photo' => $evidence_photo
        ];
        
        // Add report
        $result = addCounterfeitReport($report);
        
        if($result){
            // Send email notification to admin
            $userName = $_SESSION['username'] ?? 'User';
            $medicineName = $medicine ? $medicine['medicine_name'] : "Unknown Medicine (Barcode: $barcode)";
            sendAdminCounterfeitAlert($result, $medicineName, $userName);
            
            $_SESSION['success'] = "Counterfeit report submitted successfully! Our admin team will review it soon.";
            header('location: ../Views/report_counterfeit.php');
        }else{
            $_SESSION['error'] = "Failed to submit report. Please try again.";
            header('location: ../Views/report_counterfeit.php');
        }
        
    }else{
        header('location: ../Views/report_counterfeit.php');
    }
?>
