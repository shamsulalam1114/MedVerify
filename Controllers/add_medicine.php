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
    
    if(isset($_POST['submit'])){
        $medicine_name = trim($_REQUEST['medicine_name']);
        $generic_name = trim($_REQUEST['generic_name']);
        $manufacturer_id = $_REQUEST['manufacturer_id'];
        $category = $_REQUEST['category'];
        $dosage_form = $_REQUEST['dosage_form'];
        $strength = trim($_REQUEST['strength']);
        $barcode = trim($_REQUEST['barcode']);
        $batch_number = trim($_REQUEST['batch_number']);
        $manufacturing_date = $_REQUEST['manufacturing_date'];
        $expiry_date = $_REQUEST['expiry_date'];
        $mrp = $_REQUEST['mrp'];
        $description = trim($_REQUEST['description']);
        $composition = trim($_REQUEST['composition']);
        $prescription_required = $_REQUEST['prescription_required'];
        $status = $_REQUEST['status'];
        
        // Validation
        if($medicine_name == ""){
            $_SESSION['error'] = "Medicine name is required!";
            header('location: ../Views/add_medicine.php');
            exit();
        }
        
        if($manufacturer_id == ""){
            $_SESSION['error'] = "Manufacturer is required!";
            header('location: ../Views/add_medicine.php');
            exit();
        }
        
        if($barcode == ""){
            $_SESSION['error'] = "Barcode is required!";
            header('location: ../Views/add_medicine.php');
            exit();
        }
        
        if(strlen($barcode) < 10 || strlen($barcode) > 13){
            $_SESSION['error'] = "Barcode must be 10-13 digits!";
            header('location: ../Views/add_medicine.php');
            exit();
        }
        
        if($expiry_date <= $manufacturing_date){
            $_SESSION['error'] = "Expiry date must be after manufacturing date!";
            header('location: ../Views/add_medicine.php');
            exit();
        }
        
        // Prepare medicine data
        $medicine = [
            'medicine_name' => $medicine_name,
            'generic_name' => $generic_name,
            'manufacturer_id' => $manufacturer_id,
            'category' => $category,
            'dosage_form' => $dosage_form,
            'strength' => $strength,
            'barcode' => $barcode,
            'batch_number' => $batch_number,
            'manufacturing_date' => $manufacturing_date,
            'expiry_date' => $expiry_date,
            'mrp' => $mrp,
            'description' => $description,
            'composition' => $composition,
            'prescription_required' => $prescription_required,
            'status' => $status
        ];
        
        // Add medicine
        $result = addMedicine($medicine);
        
        if($result){
            $_SESSION['success'] = "Medicine added successfully!";
            header('location: ../Views/manage_medicines.php');
        }else{
            $_SESSION['error'] = "Failed to add medicine. Barcode might already exist.";
            header('location: ../Views/add_medicine.php');
        }
        
    }else{
        header('location: ../Views/add_medicine.php');
    }
?>
