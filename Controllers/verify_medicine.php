<?php
    session_start();
    require_once('../Models/medicineModel.php');
    require_once('../Models/medicineVerificationModel.php');
    
    if(!isset($_SESSION['user_id'])){
        header('location: ../Views/login.php');
        exit();
    }
    
    if(isset($_POST['submit'])){
        $user_id = $_SESSION['user_id'];
        $verification_method = $_REQUEST['verification_method'];
        $barcode_scanned = trim($_REQUEST['barcode_scanned']);
        $batch_number_entered = trim($_REQUEST['batch_number_entered']);
        
        // Initialize verification data
        $verification_result = 'Not Found';
        $expiry_check = 'Not Available';
        $manufacturer_match = 'Not Available';
        $batch_match = 'Not Available';
        $medicine_id = null;
        $verification_notes = '';
        $confidence_score = 0;
        
        // Validate input
        if($barcode_scanned == "" && $batch_number_entered == ""){
            $_SESSION['error'] = "Please enter barcode or batch number!";
            header('location: ../Views/verify_medicine.php');
            exit();
        }
        
        // Search medicine by barcode
        if($barcode_scanned != ""){
            $medicine = getMedicineByBarcode($barcode_scanned);
            
            if($medicine){
                $medicine_id = $medicine['medicine_id'];
                
                // Check expiry date
                $expiry_status = checkMedicineExpiry($medicine['expiry_date']);
                $expiry_check = $expiry_status;
                
                // Check manufacturer verification
                if($medicine['manufacturer_verified'] == 'Yes'){
                    $manufacturer_match = 'Match';
                }else{
                    $manufacturer_match = 'Mismatch';
                }
                
                // Check batch number if provided
                if($batch_number_entered != ""){
                    if($medicine['batch_number'] == $batch_number_entered){
                        $batch_match = 'Match';
                    }else{
                        $batch_match = 'Mismatch';
                    }
                }else{
                    $batch_match = 'Not Available';
                }
                
                // Determine verification result
                if($expiry_status == 'Expired'){
                    $verification_result = 'Expired';
                    $confidence_score = 50;
                    $verification_notes = 'Medicine found but EXPIRED. Do not use!';
                }else if($medicine['status'] == 'Recalled'){
                    $verification_result = 'Counterfeit';
                    $confidence_score = 90;
                    $verification_notes = 'This medicine has been RECALLED by the manufacturer!';
                }else if($manufacturer_match == 'Mismatch'){
                    $verification_result = 'Suspicious';
                    $confidence_score = 40;
                    $verification_notes = 'Manufacturer not verified. Exercise caution.';
                }else if($batch_match == 'Mismatch'){
                    $verification_result = 'Suspicious';
                    $confidence_score = 60;
                    $verification_notes = 'Batch number does not match. Verify with pharmacist.';
                }else{
                    $verification_result = 'Genuine';
                    $confidence_score = 95;
                    $verification_notes = 'Medicine verified as genuine. Safe to use.';
                }
                
            }else{
                // Medicine not found in database
                $verification_result = 'Not Found';
                $confidence_score = 0;
                $verification_notes = 'Medicine not found in database. Could be counterfeit or unregistered.';
            }
        }
        // Search by batch number only
        else if($batch_number_entered != ""){
            $medicine = getMedicineByBatch($batch_number_entered);
            
            if($medicine){
                $medicine_id = $medicine['medicine_id'];
                
                $expiry_status = checkMedicineExpiry($medicine['expiry_date']);
                $expiry_check = $expiry_status;
                $batch_match = 'Match';
                
                if($expiry_status == 'Expired'){
                    $verification_result = 'Expired';
                    $confidence_score = 50;
                    $verification_notes = 'Medicine found but EXPIRED. Do not use!';
                }else{
                    $verification_result = 'Genuine';
                    $confidence_score = 85;
                    $verification_notes = 'Medicine batch verified. Check expiry date on package.';
                }
            }else{
                $verification_result = 'Not Found';
                $confidence_score = 0;
                $verification_notes = 'Batch number not found in database.';
            }
        }
        
        // Get user IP address
        $ip_address = $_SERVER['REMOTE_ADDR'];
        
        // Prepare verification data
        $verification = [
            'user_id' => $user_id,
            'medicine_id' => $medicine_id,
            'barcode_scanned' => $barcode_scanned,
            'batch_number_entered' => $batch_number_entered,
            'verification_method' => $verification_method,
            'verification_result' => $verification_result,
            'confidence_score' => $confidence_score,
            'expiry_check' => $expiry_check,
            'manufacturer_match' => $manufacturer_match,
            'batch_match' => $batch_match,
            'image_uploaded' => '',
            'verification_notes' => $verification_notes,
            'ip_address' => $ip_address,
            'location' => ''
        ];
        
        // Save verification
        $result = addMedicineVerification($verification);
        
        if($result){
            // Create alert if counterfeit or suspicious
            if($verification_result == 'Counterfeit' || $verification_result == 'Suspicious'){
                $alert = [
                    'verification_id' => $result,
                    'alert_type' => $verification_result == 'Counterfeit' ? 'Counterfeit Detected' : 'Suspicious Pattern',
                    'severity' => $verification_result == 'Counterfeit' ? 'High' : 'Medium',
                    'alert_message' => $verification_notes
                ];
                addVerificationAlert($alert);
            }
            
            // Redirect to result page
            $_SESSION['verification_id'] = $result;
            header('location: ../Views/verification_result.php?id='.$result);
        }else{
            $_SESSION['error'] = "Verification failed. Please try again.";
            header('location: ../Views/verify_medicine.php');
        }
        
    }else{
        header('location: ../Views/verify_medicine.php');
    }
?>
