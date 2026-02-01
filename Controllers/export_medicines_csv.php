<?php
    session_start();
    require_once('../Models/medicineModel.php');
    require_once('../Models/manufacturerModel.php');
    
    if(!isset($_SESSION['user_id'])){
        header('location: ../Views/login.php');
        exit();
    }
    
    if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin'){
        header('location: ../Views/verify_medicine.php');
        exit();
    }
    
    // Get search parameter
    $search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
    
    // Get all medicines
    if($search_query != ''){
        $medicines = searchMedicineByName($search_query);
    }else{
        $medicines = getAllMedicines();
    }
    
    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=medicines_database_' . date('Y-m-d') . '.csv');
    
    // Create output stream
    $output = fopen('php://output', 'w');
    
    // Add BOM for UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Add headers
    fputcsv($output, [
        'Medicine ID',
        'Medicine Name',
        'Generic Name',
        'Manufacturer',
        'Country',
        'Category',
        'Dosage Form',
        'Strength',
        'Barcode',
        'Batch Number',
        'Manufacturing Date',
        'Expiry Date',
        'MRP',
        'Description',
        'Prescription Required',
        'Status'
    ]);
    
    // Add data rows
    foreach($medicines as $medicine){
        fputcsv($output, [
            $medicine['medicine_id'],
            $medicine['medicine_name'],
            $medicine['generic_name'],
            $medicine['manufacturer_name'],
            $medicine['country'],
            $medicine['category'],
            $medicine['dosage_form'],
            $medicine['strength'],
            $medicine['barcode'],
            $medicine['batch_number'],
            $medicine['manufacturing_date'],
            $medicine['expiry_date'],
            $medicine['mrp'],
            $medicine['description'],
            $medicine['prescription_required'],
            $medicine['status']
        ]);
    }
    
    fclose($output);
    exit();
?>
