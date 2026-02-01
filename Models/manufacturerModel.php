<?php
require_once('db.php');

// Get all manufacturers
function getAllManufacturers(){
    $con = getConnection();
    $sql = "SELECT * FROM manufacturers WHERE status = 'Active' ORDER BY manufacturer_name ASC";
    $result = mysqli_query($con, $sql);
    
    $manufacturers = [];
    while($row = mysqli_fetch_assoc($result)){
        array_push($manufacturers, $row);
    }
    
    return $manufacturers;
}

// Get manufacturer by ID
function getManufacturerById($id){
    $con = getConnection();
    $sql = "SELECT * FROM manufacturers WHERE manufacturer_id = '$id'";
    $result = mysqli_query($con, $sql);
    
    if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);
        return $row;
    }else{
        return false;
    }
}

// Get manufacturer by name
function getManufacturerByName($name){
    $con = getConnection();
    $sql = "SELECT * FROM manufacturers WHERE manufacturer_name = '$name'";
    $result = mysqli_query($con, $sql);
    
    if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);
        return $row;
    }else{
        return false;
    }
}

// Add new manufacturer (Admin only)
function addManufacturer($manufacturer){
    $con = getConnection();
    $sql = "INSERT INTO manufacturers (manufacturer_name, country, license_number, contact_email, contact_phone, website, is_verified, status) 
            VALUES ('{$manufacturer['manufacturer_name']}', '{$manufacturer['country']}', '{$manufacturer['license_number']}', '{$manufacturer['contact_email']}', '{$manufacturer['contact_phone']}', '{$manufacturer['website']}', '{$manufacturer['is_verified']}', '{$manufacturer['status']}')";
    
    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
}

// Update manufacturer
function updateManufacturer($manufacturer){
    $con = getConnection();
    $sql = "UPDATE manufacturers 
            SET manufacturer_name='{$manufacturer['manufacturer_name']}', 
                country='{$manufacturer['country']}', 
                license_number='{$manufacturer['license_number']}', 
                contact_email='{$manufacturer['contact_email']}', 
                contact_phone='{$manufacturer['contact_phone']}', 
                website='{$manufacturer['website']}', 
                is_verified='{$manufacturer['is_verified']}', 
                status='{$manufacturer['status']}' 
            WHERE manufacturer_id='{$manufacturer['manufacturer_id']}'";
    
    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
}

// Delete manufacturer
function deleteManufacturer($id){
    $con = getConnection();
    $sql = "DELETE FROM manufacturers WHERE manufacturer_id='$id'";
    
    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
}

// Get verified manufacturers only
function getVerifiedManufacturers(){
    $con = getConnection();
    $sql = "SELECT * FROM manufacturers WHERE is_verified = 'Yes' AND status = 'Active' ORDER BY manufacturer_name ASC";
    $result = mysqli_query($con, $sql);
    
    $manufacturers = [];
    while($row = mysqli_fetch_assoc($result)){
        array_push($manufacturers, $row);
    }
    
    return $manufacturers;
}

// Get total manufacturers count
function getTotalManufacturersCount(){
    $con = getConnection();
    $sql = "SELECT COUNT(*) as total FROM manufacturers WHERE status = 'Active'";
    $result = mysqli_query($con, $sql);
    
    if($row = mysqli_fetch_assoc($result)){
        return $row['total'];
    }
    return 0;
}

?>
