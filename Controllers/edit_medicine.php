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
        $medicine_id = $_REQUEST['medicine_id'];
        $medicine_name = trim($_REQUEST['medicine_name']);
        $generic_name = trim($_REQUEST['generic_name']);
        $manufacturer_id = $_REQUEST['manufacturer_id'];
        $category = $_REQUEST['category'];
        $dosage_form = $_REQUEST['dosage_form'];
        $strength = trim($_REQUEST['strength']);
        $mrp = $_REQUEST['mrp'];
        $description = trim($_REQUEST['description']);
        $prescription_required = $_REQUEST['prescription_required'];
        $status = $_REQUEST['status'];
        
        // Validation
        if($medicine_name == ""){
            $_SESSION['error'] = "Medicine name is required!";
            header('location: ../Views/edit_medicine.php?id='.$medicine_id);
            exit();
        }
        
        // Prepare medicine data
        $medicine = [
            'medicine_id' => $medicine_id,
            'medicine_name' => $medicine_name,
            'generic_name' => $generic_name,
            'manufacturer_id' => $manufacturer_id,
            'category' => $category,
            'dosage_form' => $dosage_form,
            'strength' => $strength,
            'mrp' => $mrp,
            'description' => $description,
            'prescription_required' => $prescription_required,
            'status' => $status
        ];
        
        // Update medicine
        $result = updateMedicine($medicine);
        
        if($result){
            $_SESSION['success'] = "Medicine updated successfully!";
            header('location: ../Views/manage_medicines.php');
        }else{
            $_SESSION['error'] = "Failed to update medicine!";
            header('location: ../Views/edit_medicine.php?id='.$medicine_id);
        }
        
    }else{
        header('location: ../Views/manage_medicines.php');
    }
?>
