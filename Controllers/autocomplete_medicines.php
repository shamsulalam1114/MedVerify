<?php
require_once('../Models/db.php');

header('Content-Type: application/json');

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if(strlen($query) < 2){
    echo json_encode([]);
    exit();
}

$conn = connect();

$sql = "SELECT DISTINCT medicine_name FROM medicines WHERE medicine_name LIKE ? OR generic_name LIKE ? LIMIT 10";
$stmt = $conn->prepare($sql);
$searchTerm = '%' . $query . '%';
$stmt->bind_param("ss", $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$suggestions = [];
while($row = $result->fetch_assoc()){
    $suggestions[] = $row['medicine_name'];
}

$stmt->close();
$conn->close();

echo json_encode($suggestions);
?>
