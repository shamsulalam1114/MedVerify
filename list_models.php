<?php
require_once('Models/geminiAI.php');

echo "<h2>List Available Gemini Models</h2>";

$listModelsUrl = 'https://generativelanguage.googleapis.com/v1beta/models?key=' . GEMINI_API_KEY;

$ch = curl_init($listModelsUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$modelsResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p><strong>HTTP Status:</strong> $httpCode</p>";

if ($httpCode == 200) {
    $modelsData = json_decode($modelsResponse, true);
    if (isset($modelsData['models'])) {
        echo "<h3>Gemini Models Available:</h3>";
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Model Name</th><th>Supported Methods</th></tr>";
        
        foreach ($modelsData['models'] as $model) {
            if (isset($model['name']) && strpos($model['name'], 'gemini') !== false) {
                $modelName = str_replace('models/', '', $model['name']);
                $supportedMethods = isset($model['supportedGenerationMethods']) ? implode(', ', $model['supportedGenerationMethods']) : 'N/A';
                
                echo "<tr>";
                echo "<td><strong>$modelName</strong></td>";
                echo "<td>$supportedMethods</td>";
                echo "</tr>";
            }
        }
        echo "</table>";
        
        echo "<h3>Full Response:</h3>";
        echo "<textarea rows='20' cols='100' style='font-family: monospace; width: 100%;'>";
        echo json_encode($modelsData, JSON_PRETTY_PRINT);
        echo "</textarea>";
    }
} else {
    echo "<p style='color: red;'><strong>Error:</strong> $modelsResponse</p>";
}
?>
