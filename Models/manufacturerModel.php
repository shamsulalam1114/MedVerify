<?php
require_once('db.php');

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

function addManufacturer($manufacturer){
    $con = getConnection();

    $name = mysqli_real_escape_string($con, $manufacturer['name']);
    $country = mysqli_real_escape_string($con, $manufacturer['country']);
    $address = mysqli_real_escape_string($con, $manufacturer['address'] ?? '');
    $website = mysqli_real_escape_string($con, $manufacturer['website'] ?? '');
    $licenseNumber = mysqli_real_escape_string($con, $manufacturer['license_number']);
    $licenseExpiry = $manufacturer['license_expiry'] ? "'{$manufacturer['license_expiry']}'" : 'NULL';
    $certifications = mysqli_real_escape_string($con, $manufacturer['certifications'] ?? '');
    $contactEmail = mysqli_real_escape_string($con, $manufacturer['contact_email'] ?? '');
    $contactPhone = mysqli_real_escape_string($con, $manufacturer['contact_phone'] ?? '');
    $status = mysqli_real_escape_string($con, $manufacturer['status']);

    $sql = "INSERT INTO manufacturers (manufacturer_name, country, address, website, license_number, 
                license_expiry, certifications, contact_email, contact_phone, is_verified, status) 
            VALUES ('$name', '$country', '$address', '$website', '$licenseNumber', 
                $licenseExpiry, '$certifications', '$contactEmail', '$contactPhone', 'No', '$status')";

    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
}

function updateManufacturer($manufacturer){
    $con = getConnection();

    $id = mysqli_real_escape_string($con, $manufacturer['manufacturer_id']);
    $name = mysqli_real_escape_string($con, $manufacturer['name']);
    $country = mysqli_real_escape_string($con, $manufacturer['country']);
    $address = mysqli_real_escape_string($con, $manufacturer['address'] ?? '');
    $website = mysqli_real_escape_string($con, $manufacturer['website'] ?? '');
    $licenseNumber = mysqli_real_escape_string($con, $manufacturer['license_number']);
    $licenseExpiry = $manufacturer['license_expiry'] ? "'{$manufacturer['license_expiry']}'" : 'NULL';
    $certifications = mysqli_real_escape_string($con, $manufacturer['certifications'] ?? '');
    $contactEmail = mysqli_real_escape_string($con, $manufacturer['contact_email'] ?? '');
    $contactPhone = mysqli_real_escape_string($con, $manufacturer['contact_phone'] ?? '');
    $status = mysqli_real_escape_string($con, $manufacturer['status']);

    $sql = "UPDATE manufacturers 
            SET manufacturer_name='$name', 
                country='$country', 
                address='$address',
                website='$website',
                license_number='$licenseNumber', 
                license_expiry=$licenseExpiry,
                certifications='$certifications',
                contact_email='$contactEmail', 
                contact_phone='$contactPhone', 
                status='$status' 
            WHERE manufacturer_id='$id'";

    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
}

function deleteManufacturer($id){
    $con = getConnection();
    $sql = "DELETE FROM manufacturers WHERE manufacturer_id='$id'";

    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
}

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

function getTotalManufacturersCount(){
    $con = getConnection();
    $sql = "SELECT COUNT(*) as total FROM manufacturers WHERE status = 'Active'";
    $result = mysqli_query($con, $sql);

    if($row = mysqli_fetch_assoc($result)){
        return $row['total'];
    }
    return 0;
}

function getAllManufacturersWithStats($searchQuery = '', $statusFilter = 'All'){
    $con = getConnection();

    $sql = "SELECT 
                m.*,
                m.manufacturer_name as name,
                COUNT(DISTINCT med.medicine_id) as medicines_count,
                COALESCE(ROUND((COUNT(DISTINCT CASE WHEN mv.is_manufacturer_verified = 'Yes' THEN mv.verification_id END) * 100.0) / 
                    NULLIF(COUNT(DISTINCT mv.verification_id), 0), 2), 0) as verification_rate
            FROM manufacturers m
            LEFT JOIN medicines med ON m.manufacturer_id = med.manufacturer_id
            LEFT JOIN medicine_verifications mv ON med.medicine_id = mv.medicine_id
            WHERE 1=1";

    if (!empty($searchQuery)) {
        $searchQuery = mysqli_real_escape_string($con, $searchQuery);
        $sql .= " AND (m.manufacturer_name LIKE '%$searchQuery%' 
                    OR m.country LIKE '%$searchQuery%' 
                    OR m.license_number LIKE '%$searchQuery%')";
    }

    if ($statusFilter !== 'All') {
        $statusFilter = mysqli_real_escape_string($con, $statusFilter);
        $sql .= " AND m.status = '$statusFilter'";
    }

    $sql .= " GROUP BY m.manufacturer_id ORDER BY m.manufacturer_name ASC";

    $result = mysqli_query($con, $sql);

    $manufacturers = [];
    while($row = mysqli_fetch_assoc($result)){
        array_push($manufacturers, $row);
    }

    return $manufacturers;
}

function getManufacturerStatistics(){
    $con = getConnection();

    $stats = [
        'total_manufacturers' => 0,
        'active_manufacturers' => 0,
        'verified_manufacturers' => 0,
        'total_medicines' => 0
    ];

    $sql = "SELECT COUNT(*) as total FROM manufacturers";
    $result = mysqli_query($con, $sql);
    if($row = mysqli_fetch_assoc($result)){
        $stats['total_manufacturers'] = $row['total'];
    }

    $sql = "SELECT COUNT(*) as total FROM manufacturers WHERE status = 'Active'";
    $result = mysqli_query($con, $sql);
    if($row = mysqli_fetch_assoc($result)){
        $stats['active_manufacturers'] = $row['total'];
    }

    $sql = "SELECT COUNT(*) as total FROM manufacturers WHERE is_verified = 'Yes'";
    $result = mysqli_query($con, $sql);
    if($row = mysqli_fetch_assoc($result)){
        $stats['verified_manufacturers'] = $row['total'];
    }

    $sql = "SELECT COUNT(*) as total FROM medicines";
    $result = mysqli_query($con, $sql);
    if($row = mysqli_fetch_assoc($result)){
        $stats['total_medicines'] = $row['total'];
    }

    return $stats;
}

function manufacturerLicenseExists($licenseNumber){
    $con = getConnection();
    $licenseNumber = mysqli_real_escape_string($con, $licenseNumber);
    $sql = "SELECT COUNT(*) as count FROM manufacturers WHERE license_number = '$licenseNumber'";
    $result = mysqli_query($con, $sql);

    if($row = mysqli_fetch_assoc($result)){
        return $row['count'] > 0;
    }
    return false;
}

function manufacturerLicenseExistsExcept($licenseNumber, $manufacturerId){
    $con = getConnection();
    $licenseNumber = mysqli_real_escape_string($con, $licenseNumber);
    $sql = "SELECT COUNT(*) as count FROM manufacturers 
            WHERE license_number = '$licenseNumber' AND manufacturer_id != '$manufacturerId'";
    $result = mysqli_query($con, $sql);

    if($row = mysqli_fetch_assoc($result)){
        return $row['count'] > 0;
    }
    return false;
}

function getManufacturerMedicineCount($manufacturerId){
    $con = getConnection();
    $sql = "SELECT COUNT(*) as count FROM medicines WHERE manufacturer_id = '$manufacturerId'";
    $result = mysqli_query($con, $sql);

    if($row = mysqli_fetch_assoc($result)){
        return $row['count'];
    }
    return 0;
}

?>
