<?php
// Error Testing Script
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>MedVerify Error Checker</h2>";
echo "<pre>";

// Test 1: Database Connection
echo "\n1. Testing Database Connection...\n";
require_once('Models/db.php');
$conn = getConnection();
if($conn){
    echo "   ✅ Database connection successful\n";
    echo "   Database: " . mysqli_get_host_info($conn) . "\n";
} else {
    echo "   ❌ Database connection failed: " . mysqli_connect_error() . "\n";
}

// Test 2: Check if tables exist
echo "\n2. Checking Database Tables...\n";
$tables = ['users', 'medicines', 'manufacturers', 'medicine_verifications', 
           'reported_counterfeits', 'appointments', 'family_members', 'reports'];
foreach($tables as $table){
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    if(mysqli_num_rows($result) > 0){
        echo "   ✅ Table '$table' exists\n";
    } else {
        echo "   ❌ Table '$table' NOT FOUND\n";
    }
}

// Test 3: Check reported_counterfeits structure
echo "\n3. Checking 'reported_counterfeits' table structure...\n";
$result = mysqli_query($conn, "DESCRIBE reported_counterfeits");
if($result){
    $columns = [];
    while($row = mysqli_fetch_assoc($result)){
        $columns[] = $row['Field'];
    }
    echo "   Columns: " . implode(", ", $columns) . "\n";
    
    if(in_array('report_medicine_id', $columns)){
        echo "   ✅ Column 'report_medicine_id' exists\n";
    } else if(in_array('medicine_id', $columns)){
        echo "   ⚠️  Column 'medicine_id' exists (should be 'report_medicine_id')\n";
        echo "   Run: ALTER TABLE reported_counterfeits CHANGE medicine_id report_medicine_id INT;\n";
    } else {
        echo "   ❌ No medicine reference column found\n";
    }
}

// Test 4: Load Model Files
echo "\n4. Testing Model Files...\n";
$models = [
    'Models/userModel.php',
    'Models/medicineModel.php',
    'Models/manufacturerModel.php',
    'Models/counterfeitModel.php',
    'Models/verificationModel.php',
    'Models/medicineVerificationModel.php'
];

foreach($models as $model){
    if(file_exists($model)){
        try {
            require_once($model);
            echo "   ✅ $model loaded successfully\n";
        } catch (Exception $e){
            echo "   ❌ $model error: " . $e->getMessage() . "\n";
        }
    } else {
        echo "   ❌ $model NOT FOUND\n";
    }
}

// Test 5: Check for duplicate functions
echo "\n5. Checking for duplicate functions in medicineModel.php...\n";
$content = file_get_contents('Models/medicineModel.php');
preg_match_all('/function\s+(\w+)\s*\(/', $content, $matches);
$functions = $matches[1];
$counts = array_count_values($functions);
$duplicates = array_filter($counts, function($count){ return $count > 1; });
if(empty($duplicates)){
    echo "   ✅ No duplicate functions found\n";
} else {
    echo "   ❌ Duplicate functions found:\n";
    foreach($duplicates as $func => $count){
        echo "      - $func appears $count times\n";
    }
}

// Test 6: Test counterfeitModel functions
echo "\n6. Testing counterfeitModel functions...\n";
try {
    $reports = getAllCounterfeitReports();
    echo "   ✅ getAllCounterfeitReports() works - Found " . count($reports) . " reports\n";
} catch (Exception $e){
    echo "   ❌ getAllCounterfeitReports() error: " . $e->getMessage() . "\n";
}

echo "\n</pre>";
echo "<p><strong>Test Complete!</strong></p>";
echo "<p><a href='Views/dashboard.php'>Go to Dashboard</a></p>";
?>
