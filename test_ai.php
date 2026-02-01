<?php
require_once('Models/geminiAI.php');

echo "<h2>Testing Gemini Pro AI Connection</h2>";

// Test 1: Simple text generation
echo "<h3>Test 1: Gemini Pro Text API</h3>";
$testBarcode = "8901396316084";
$result = validateBarcodeWithAI($testBarcode);
echo "<pre>";
print_r($result);
echo "</pre>";

// Test 2: Image analysis (if you have a test image)
echo "<h3>Test 2: Check API Key Status</h3>";
if (defined('GEMINI_API_KEY')) {
    echo "✅ API Key loaded: " . substr(GEMINI_API_KEY, 0, 20) . "...<br>";
} else {
    echo "❌ API Key not found<br>";
}

echo "<h3>Test 3: cURL Availability</h3>";
if (function_exists('curl_init')) {
    echo "✅ cURL is enabled<br>";
} else {
    echo "❌ cURL is NOT enabled - AI won't work!<br>";
}

echo "<h3>Test 4: SSL/HTTPS Support</h3>";
$ch = curl_init('https://www.google.com');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

if ($result) {
    echo "✅ HTTPS requests working<br>";
} else {
    echo "❌ HTTPS Error: $error<br>";
}
?>
