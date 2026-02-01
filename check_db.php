<?php
require_once('Models/db.php');

$conn = getConnection();

echo "<h2>Database Column Check: reported_counterfeits</h2>";
echo "<pre>";

$result = mysqli_query($conn, "DESCRIBE reported_counterfeits");

if($result){
    echo "Table Structure:\n";
    echo str_pad("Field", 30) . str_pad("Type", 20) . str_pad("Null", 10) . str_pad("Key", 10) . "Extra\n";
    echo str_repeat("-", 80) . "\n";
    
    while($row = mysqli_fetch_assoc($result)){
        echo str_pad($row['Field'], 30) . 
             str_pad($row['Type'], 20) . 
             str_pad($row['Null'], 10) . 
             str_pad($row['Key'], 10) . 
             $row['Extra'] . "\n";
    }
} else {
    echo "Error: " . mysqli_error($conn);
}

echo "\n\nTest Query:\n";
$testQuery = "SELECT rc.*, m.medicine_name 
            FROM reported_counterfeits rc 
            LEFT JOIN medicines m ON rc.medicine_id = m.medicine_id 
            LIMIT 1";
            
echo "Executing: $testQuery\n\n";

$result = mysqli_query($conn, $testQuery);
if($result){
    echo "✅ Query executed successfully!\n";
    if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);
        echo "Sample data columns: " . implode(", ", array_keys($row)) . "\n";
    } else {
        echo "No records found (table might be empty)\n";
    }
} else {
    echo "❌ Query failed!\n";
    echo "Error: " . mysqli_error($conn) . "\n";
}

echo "</pre>";
?>
