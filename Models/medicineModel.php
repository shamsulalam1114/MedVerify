<?php
require_once('db.php');

// Get all medicines (including inactive)
function getAllMedicines(){
    $con = getConnection();
    $sql = "SELECT m.*, mf.manufacturer_name, mf.country 
            FROM medicines m 
            LEFT JOIN manufacturers mf ON m.manufacturer_id = mf.manufacturer_id 
            ORDER BY m.medicine_name ASC";
    $result = mysqli_query($con, $sql);
    
    $medicines = [];
    while($row = mysqli_fetch_assoc($result)){
        array_push($medicines, $row);
    }
    
    return $medicines;
}

// Get all active medicines only
function getActiveMedicines(){
    $con = getConnection();
    $sql = "SELECT m.*, mf.manufacturer_name, mf.country 
            FROM medicines m 
            LEFT JOIN manufacturers mf ON m.manufacturer_id = mf.manufacturer_id 
            WHERE m.status = 'Active' 
            ORDER BY m.medicine_name ASC";
    $result = mysqli_query($con, $sql);
    
    $medicines = [];
    while($row = mysqli_fetch_assoc($result)){
        array_push($medicines, $row);
    }
    
    return $medicines;
}

// Get medicine by ID
function getMedicineById($id){
    $con = getConnection();
    $sql = "SELECT m.*, mf.manufacturer_name, mf.country 
            FROM medicines m 
            LEFT JOIN manufacturers mf ON m.manufacturer_id = mf.manufacturer_id 
            WHERE m.medicine_id = '$id'";
    $result = mysqli_query($con, $sql);
    
    if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);
        return $row;
    }else{
        return false;
    }
}

// Get medicine by barcode
function getMedicineByBarcode($barcode){
    $con = getConnection();
    $sql = "SELECT m.*, mf.manufacturer_name, mf.country, mf.is_verified as manufacturer_verified 
            FROM medicines m 
            LEFT JOIN manufacturers mf ON m.manufacturer_id = mf.manufacturer_id 
            WHERE m.barcode = '$barcode'";
    $result = mysqli_query($con, $sql);
    
    if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);
        return $row;
    }else{
        return false;
    }
}

// Search medicine by name
function searchMedicineByName($name){
    $con = getConnection();
    $sql = "SELECT m.*, mf.manufacturer_name 
            FROM medicines m 
            LEFT JOIN manufacturers mf ON m.manufacturer_id = mf.manufacturer_id 
            WHERE m.medicine_name LIKE '%$name%' 
            OR m.generic_name LIKE '%$name%' 
            AND m.status = 'Active' 
            LIMIT 20";
    $result = mysqli_query($con, $sql);
    
    $medicines = [];
    while($row = mysqli_fetch_assoc($result)){
        array_push($medicines, $row);
    }
    
    return $medicines;
}

// Get medicine by batch number
function getMedicineByBatch($batch_number){
    $con = getConnection();
    $sql = "SELECT m.*, mf.manufacturer_name, mb.batch_number, mb.manufacturing_date, mb.expiry_date, mb.status as batch_status 
            FROM medicines m 
            LEFT JOIN manufacturers mf ON m.manufacturer_id = mf.manufacturer_id 
            LEFT JOIN medicine_batches mb ON m.medicine_id = mb.medicine_id 
            WHERE mb.batch_number = '$batch_number'";
    $result = mysqli_query($con, $sql);
    
    if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);
        return $row;
    }else{
        return false;
    }
}

// Add new medicine (Admin only)
function addMedicine($medicine){
    $con = getConnection();
    $sql = "INSERT INTO medicines (medicine_name, generic_name, manufacturer_id, category, dosage_form, strength, barcode, batch_number, manufacturing_date, expiry_date, mrp, description, composition, prescription_required, status) 
            VALUES ('{$medicine['medicine_name']}', '{$medicine['generic_name']}', '{$medicine['manufacturer_id']}', '{$medicine['category']}', '{$medicine['dosage_form']}', '{$medicine['strength']}', '{$medicine['barcode']}', '{$medicine['batch_number']}', '{$medicine['manufacturing_date']}', '{$medicine['expiry_date']}', '{$medicine['mrp']}', '{$medicine['description']}', '{$medicine['composition']}', '{$medicine['prescription_required']}', '{$medicine['status']}')";
    
    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
}

// Update medicine
function updateMedicine($medicine){
    $con = getConnection();
    $sql = "UPDATE medicines 
            SET medicine_name='{$medicine['medicine_name']}', 
                generic_name='{$medicine['generic_name']}', 
                manufacturer_id='{$medicine['manufacturer_id']}', 
                category='{$medicine['category']}', 
                dosage_form='{$medicine['dosage_form']}', 
                strength='{$medicine['strength']}', 
                mrp='{$medicine['mrp']}', 
                description='{$medicine['description']}', 
                prescription_required='{$medicine['prescription_required']}', 
                status='{$medicine['status']}' 
            WHERE medicine_id='{$medicine['medicine_id']}'";
    
    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
}

// Delete medicine (Admin only)
function deleteMedicine($id){
    $con = getConnection();
    $sql = "DELETE FROM medicines WHERE medicine_id='$id'";
    
    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
}

// Get total medicines count
function getTotalMedicinesCount(){
    $con = getConnection();
    $sql = "SELECT COUNT(*) as total FROM medicines";
    $result = mysqli_query($con, $sql);
    
    if($row = mysqli_fetch_assoc($result)){
        return $row['total'];
    }
    return 0;
}

// Get medicines by category
function getMedicinesByCategory($category){
    $con = getConnection();
    $sql = "SELECT m.*, mf.manufacturer_name 
            FROM medicines m 
            LEFT JOIN manufacturers mf ON m.manufacturer_id = mf.manufacturer_id 
            WHERE m.category = '$category' AND m.status = 'Active' 
            ORDER BY m.medicine_name ASC";
    $result = mysqli_query($con, $sql);
    
    $medicines = [];
    while($row = mysqli_fetch_assoc($result)){
        array_push($medicines, $row);
    }
    
    return $medicines;
}

// Check if medicine is expired
function checkMedicineExpiry($expiry_date){
    $today = date('Y-m-d');
    $expiry = date('Y-m-d', strtotime($expiry_date));
    
    if($expiry < $today){
        return 'Expired';
    }else if($expiry <= date('Y-m-d', strtotime('+90 days'))){
        return 'Near Expiry';
    }else{
        return 'Valid';
    }
}

// Get expiring medicines (within 90 days)
function getExpiringMedicines(){
    $con = getConnection();
    $sql = "SELECT m.medicine_id, m.medicine_name, mf.manufacturer_name, mb.batch_number, mb.expiry_date, 
            DATEDIFF(mb.expiry_date, CURDATE()) as days_to_expiry 
            FROM medicines m 
            JOIN manufacturers mf ON m.manufacturer_id = mf.manufacturer_id 
            JOIN medicine_batches mb ON m.medicine_id = mb.medicine_id 
            WHERE mb.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 90 DAY) 
            AND mb.status = 'In Stock' 
            ORDER BY mb.expiry_date ASC";
    $result = mysqli_query($con, $sql);
    
    $medicines = [];
    while($row = mysqli_fetch_assoc($result)){
        array_push($medicines, $row);
    }
    
    return $medicines;
}

?>
