<?php
require_once('Models/geminiAI.php');

echo "<h2>Testing Gemini Pro AI Connection</h2>";

echo "<h3>Test 1: Direct Gemini API Test</h3>";
$prompt = "Say 'Hello, I am Gemini Pro AI working correctly!' if you can read this.";

$requestBody = [
    'contents' => [
        [
            'parts' => [
                ['text' => $prompt]
            ]
        ]
    ]
];

$apiUrl = GEMINI_API_URL . '?key=' . GEMINI_API_KEY;

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "<b>HTTP Status Code:</b> $httpCode<br>";

if ($error) {
    echo "<b style='color: red;'>cURL Error:</b> $error<br>";
}

echo "<b>Raw API Response:</b><br>";
echo "<textarea style='width: 100%; height: 200px;'>";
echo htmlspecialchars($response);
echo "</textarea><br><br>";

if ($httpCode == 200) {
    $data = json_decode($response, true);
    if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
        echo "<div style='background-color: #d4edda; padding: 20px; border: 2px solid green;'>";
        echo "<h3 style='color: green;'>✅ SUCCESS! Gemini AI Response:</h3>";
        echo "<p style='font-size: 18px;'><b>" . $data['candidates'][0]['content']['parts'][0]['text'] . "</b></p>";
        echo "</div>";
    } else {
        echo "<div style='background-color: #fff3cd; padding: 20px; border: 2px solid orange;'>";
        echo "<h3 style='color: orange;'>⚠️ Unexpected Response Format</h3>";
        echo "<pre>" . print_r($data, true) . "</pre>";
        echo "</div>";
    }
} else {
    echo "<div style='background-color: #f8d7da; padding: 20px; border: 2px solid red;'>";
    echo "<h3 style='color: red;'>❌ API Error - HTTP $httpCode</h3>";
    $errorData = json_decode($response, true);
    if ($errorData) {
        echo "<pre>" . print_r($errorData, true) . "</pre>";
    }
    echo "</div>";
}

echo "<hr>";
echo "<h3>Test 2: Check API Key Status</h3>";
if (defined('GEMINI_API_KEY')) {
    echo "✅ API Key loaded: " . substr(GEMINI_API_KEY, 0, 20) . "...<br>";
    echo "Full Key (for verification): " . GEMINI_API_KEY . "<br>";
} else {
    echo "❌ API Key not found<br>";
}

echo "<h3>Test 3: cURL Availability</h3>";
if (function_exists('curl_init')) {
    echo "✅ cURL is enabled<br>";
} else {
    echo "❌ cURL is NOT enabled - AI won't work!<br>";
}

echo "<h3>Test 4: API Endpoint</h3>";
echo "Using endpoint: " . GEMINI_API_URL . "<br>";
?>
