<?php
session_start();
require_once('../Models/medicineVerificationModel.php');
require_once('../Models/medicineModel.php');
require_once('../Models/geminiAI.php');

if(!isset($_SESSION['user_id'])){
    header('location: ../Views/login.php');
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $user_id = $_SESSION['user_id'];
    $barcode = isset($_POST['barcode']) ? trim($_POST['barcode']) : '';
    $batch_number = isset($_POST['batch_number']) ? trim($_POST['batch_number']) : '';
    $verification_method = isset($_POST['method']) ? $_POST['method'] : 'Manual Entry';
    
    $imagePath = null;
    if(isset($_FILES['medicine_image']) && $_FILES['medicine_image']['error'] == 0){
        $uploadDir = '../uploads/verifications/';
        if(!is_dir($uploadDir)){
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = 'verify_' . $user_id . '_' . time() . '_' . basename($_FILES['medicine_image']['name']);
        $targetPath = $uploadDir . $fileName;
        
        if(move_uploaded_file($_FILES['medicine_image']['tmp_name'], $targetPath)){
            $imagePath = $targetPath;
        }
    }
    
    if(empty($barcode) && empty($batch_number)){
        $_SESSION['error'] = "Please enter either a barcode or batch number";
        header('location: ../Views/verify_medicine.php');
        exit();
    }
    
    $medicine = null;
    if(!empty($barcode)){
        $medicine = getMedicineByBarcode($barcode);
    }
    
    if(!$medicine && !empty($batch_number)){
        $medicine = getMedicineByBatch($batch_number);
    }
    
    $medicineData = [
        'barcode' => $barcode,
        'batch_number' => $batch_number,
        'medicine_id' => $medicine ? $medicine['medicine_id'] : null,
        'medicine_name' => $medicine ? $medicine['medicine_name'] : null,
        'manufacturer_id' => $medicine ? $medicine['manufacturer_id'] : null,
        'manufacturer' => $medicine ? $medicine['manufacturer_name'] : null,
        'price' => $medicine ? $medicine['mrp'] : null
    ];
    
    $aiReport = generateComprehensiveAIReport($medicineData, $imagePath);
    
    $verification_result = $aiReport['final_verdict'];
    $confidence_score = $aiReport['overall_confidence'];
    
    $ai_analysis = json_encode($aiReport);
    
    if($medicine){
        $expiry_check = 'Valid';
        if(isset($medicine['expiry_date'])){
            $expiry_status = checkMedicineExpiry($medicine['expiry_date']);
            if($expiry_status == 'Expired'){
                $verification_result = 'Expired';
                $expiry_check = 'Expired';
            }elseif($expiry_status == 'Near Expiry'){
                $expiry_check = 'Near Expiry';
            }
        }
        
        $verification_id = addVerification(
            $user_id,
            $medicine['medicine_id'],
            $barcode,
            $batch_number,
            $verification_method,
            $verification_result,
            $confidence_score,
            $expiry_check,
            $imagePath ? basename($imagePath) : null,
            $ai_analysis
        );
        
        if($verification_result == 'Counterfeit' || $verification_result == 'Suspicious'){
            createVerificationAlert($verification_id, $medicine['medicine_id'], $user_id);
        }
        
        $_SESSION['verification_id'] = $verification_id;
        $_SESSION['ai_enabled'] = true;
        header('location: ../Views/verification_result.php?id=' . $verification_id);
        exit();
        
    }else{
        $verification_id = addVerification(
            $user_id,
            null,
            $barcode,
            $batch_number,
            $verification_method,
            'Not Found',
            $confidence_score,
            'Unknown',
            $imagePath ? basename($imagePath) : null,
            $ai_analysis
        );
        
        $_SESSION['verification_id'] = $verification_id;
        $_SESSION['ai_enabled'] = true;
        $_SESSION['error'] = "Medicine not found in database. AI analysis completed.";
        header('location: ../Views/verification_result.php?id=' . $verification_id);
        exit();
    }
}else{
    header('location: ../Views/verify_medicine.php');
    exit();
}
?>
