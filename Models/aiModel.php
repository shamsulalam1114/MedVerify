<?php

function analyzeImageAuthenticity($imagePath) {
    if (!file_exists($imagePath)) {
        return [
            'success' => false,
            'confidence' => 0,
            'features_detected' => [],
            'authenticity_score' => 0,
            'warnings' => ['Image not found']
        ];
    }
    
    $imageData = file_get_contents($imagePath);
    $base64Image = base64_encode($imageData);
    
    $detectedFeatures = [];
    $warnings = [];
    $authenticityScore = 75;
    
    $imageInfo = getimagesize($imagePath);
    if ($imageInfo) {
        $width = $imageInfo[0];
        $height = $imageInfo[1];
        
        if ($width < 300 || $height < 300) {
            $authenticityScore -= 15;
            $warnings[] = 'Low resolution image - may affect accuracy';
        }
        
        $detectedFeatures[] = 'Image dimensions: ' . $width . 'x' . $height;
    }
    
    $exifData = @exif_read_data($imagePath);
    if ($exifData) {
        if (isset($exifData['Make'])) {
            $detectedFeatures[] = 'Camera: ' . $exifData['Make'];
        }
        if (isset($exifData['DateTime'])) {
            $detectedFeatures[] = 'Captured: ' . $exifData['DateTime'];
        }
        
        if (!isset($exifData['Make']) && !isset($exifData['Model'])) {
            $authenticityScore -= 10;
            $warnings[] = 'No camera metadata - possible edited image';
        }
    }
    
    $hashValue = md5_file($imagePath);
    $duplicateCheck = checkImageDuplicates($hashValue);
    if ($duplicateCheck['is_duplicate']) {
        $authenticityScore -= 30;
        $warnings[] = 'Duplicate image detected - previously used for verification';
    }
    
    $textDetected = performOCR($imagePath);
    if ($textDetected['success']) {
        $detectedFeatures = array_merge($detectedFeatures, $textDetected['text_found']);
        $authenticityScore += 10;
    }
    
    if ($authenticityScore > 100) $authenticityScore = 100;
    if ($authenticityScore < 0) $authenticityScore = 0;
    
    return [
        'success' => true,
        'confidence' => $authenticityScore,
        'features_detected' => $detectedFeatures,
        'authenticity_score' => $authenticityScore,
        'warnings' => $warnings,
        'image_hash' => $hashValue
    ];
}

function verifyBarcodeWithAI($barcode, $medicineId = null) {
    $validationResult = [
        'is_valid' => true,
        'confidence' => 90,
        'barcode_type' => 'Unknown',
        'warnings' => [],
        'pattern_analysis' => []
    ];
    
    $barcode = trim($barcode);
    $length = strlen($barcode);
    
    if ($length == 13) {
        $validationResult['barcode_type'] = 'EAN-13';
        $isValid = validateEAN13Checksum($barcode);
        if (!$isValid) {
            $validationResult['is_valid'] = false;
            $validationResult['confidence'] = 20;
            $validationResult['warnings'][] = 'Invalid EAN-13 checksum - likely counterfeit';
        }
    } elseif ($length == 12) {
        $validationResult['barcode_type'] = 'UPC-A';
        $isValid = validateUPCAChecksum($barcode);
        if (!$isValid) {
            $validationResult['is_valid'] = false;
            $validationResult['confidence'] = 20;
            $validationResult['warnings'][] = 'Invalid UPC-A checksum - likely counterfeit';
        }
    } elseif ($length == 8) {
        $validationResult['barcode_type'] = 'EAN-8';
    } else {
        $validationResult['warnings'][] = 'Non-standard barcode length: ' . $length;
        $validationResult['confidence'] -= 20;
    }
    
    if (!ctype_digit($barcode)) {
        $validationResult['is_valid'] = false;
        $validationResult['confidence'] = 10;
        $validationResult['warnings'][] = 'Barcode contains non-numeric characters';
    }
    
    $pattern = analyzeBarcodePattern($barcode);
    $validationResult['pattern_analysis'] = $pattern;
    
    if ($pattern['is_sequential'] || $pattern['is_repetitive']) {
        $validationResult['confidence'] -= 30;
        $validationResult['warnings'][] = 'Suspicious pattern detected - possible fake barcode';
    }
    
    if ($medicineId) {
        $historyCheck = checkBarcodeHistory($barcode, $medicineId);
        if ($historyCheck['used_by_multiple_medicines']) {
            $validationResult['confidence'] -= 40;
            $validationResult['warnings'][] = 'Barcode used for multiple different medicines';
        }
    }
    
    return $validationResult;
}

function detectCounterfeitPatterns($medicineData, $reportHistory = []) {
    $riskScore = 0;
    $riskFactors = [];
    $recommendations = [];
    
    if (isset($medicineData['manufacturer_id'])) {
        $manufacturerRisk = analyzeManufacturerRisk($medicineData['manufacturer_id']);
        if ($manufacturerRisk['high_risk']) {
            $riskScore += 30;
            $riskFactors[] = 'Manufacturer has high counterfeit report rate: ' . $manufacturerRisk['counterfeit_rate'] . '%';
        }
    }
    
    if (isset($medicineData['medicine_name'])) {
        $medicineRisk = analyzeMedicineRisk($medicineData['medicine_name']);
        if ($medicineRisk['frequently_counterfeited']) {
            $riskScore += 25;
            $riskFactors[] = 'This medicine is frequently counterfeited (' . $medicineRisk['counterfeit_count'] . ' reports)';
        }
    }
    
    if (isset($medicineData['price']) && isset($medicineData['average_price'])) {
        $priceDeviation = abs($medicineData['price'] - $medicineData['average_price']) / $medicineData['average_price'] * 100;
        if ($priceDeviation > 30) {
            $riskScore += 20;
            $riskFactors[] = 'Price deviates ' . round($priceDeviation, 1) . '% from market average';
        }
    }
    
    if (isset($medicineData['batch_number'])) {
        $batchAnalysis = analyzeBatchPattern($medicineData['batch_number']);
        if ($batchAnalysis['suspicious']) {
            $riskScore += 15;
            $riskFactors[] = 'Suspicious batch number pattern detected';
        }
    }
    
    if (count($reportHistory) > 0) {
        $patternMatch = findSimilarReports($medicineData, $reportHistory);
        if ($patternMatch['match_found']) {
            $riskScore += 35;
            $riskFactors[] = 'Similar counterfeit reports found: ' . $patternMatch['match_count'];
        }
    }
    
    if (isset($medicineData['expiry_date'])) {
        $expiryDate = strtotime($medicineData['expiry_date']);
        $monthsToExpiry = ($expiryDate - time()) / (30 * 24 * 60 * 60);
        
        if ($monthsToExpiry > 60) {
            $riskScore += 10;
            $riskFactors[] = 'Unusually long shelf life - expires in ' . round($monthsToExpiry) . ' months';
        }
    }
    
    if ($riskScore >= 70) {
        $riskLevel = 'HIGH';
        $recommendations[] = 'DO NOT USE - High probability of counterfeit';
        $recommendations[] = 'Report to local health authorities immediately';
        $recommendations[] = 'Preserve packaging for investigation';
    } elseif ($riskScore >= 40) {
        $riskLevel = 'MEDIUM';
        $recommendations[] = 'Verify with manufacturer before use';
        $recommendations[] = 'Check for additional authentication features';
        $recommendations[] = 'Consider reporting for verification';
    } else {
        $riskLevel = 'LOW';
        $recommendations[] = 'Medicine appears authentic';
        $recommendations[] = 'Continue monitoring for any irregularities';
    }
    
    return [
        'risk_score' => min($riskScore, 100),
        'risk_level' => $riskLevel,
        'risk_factors' => $riskFactors,
        'recommendations' => $recommendations,
        'ai_confidence' => max(0, 100 - $riskScore)
    ];
}

function predictVerificationOutcome($medicineData, $userHistory = []) {
    $features = extractFeatures($medicineData, $userHistory);
    
    $weights = [
        'manufacturer_reputation' => 0.25,
        'barcode_validity' => 0.20,
        'price_consistency' => 0.15,
        'batch_pattern' => 0.15,
        'user_reliability' => 0.10,
        'medicine_history' => 0.15
    ];
    
    $genuineScore = 0;
    foreach ($features as $feature => $value) {
        if (isset($weights[$feature])) {
            $genuineScore += $value * $weights[$feature];
        }
    }
    
    $genuineScore = max(0, min(100, $genuineScore));
    
    if ($genuineScore >= 75) {
        $prediction = 'Genuine';
        $confidence = $genuineScore;
    } elseif ($genuineScore >= 50) {
        $prediction = 'Suspicious';
        $confidence = 70;
    } else {
        $prediction = 'Counterfeit';
        $confidence = 100 - $genuineScore;
    }
    
    return [
        'prediction' => $prediction,
        'confidence' => round($confidence, 2),
        'genuine_probability' => round($genuineScore, 2),
        'feature_scores' => $features
    ];
}

function performOCR($imagePath) {
    $textFound = [];
    
    if (extension_loaded('gd')) {
        $imageType = exif_imagetype($imagePath);
        
        if ($imageType == IMAGETYPE_JPEG) {
            $image = imagecreatefromjpeg($imagePath);
        } elseif ($imageType == IMAGETYPE_PNG) {
            $image = imagecreatefrompng($imagePath);
        } else {
            return ['success' => false, 'text_found' => []];
        }
        
        $width = imagesx($image);
        $height = imagesy($image);
        
        $brightness = 0;
        $pixelCount = 0;
        
        for ($x = 0; $x < min($width, 100); $x += 10) {
            for ($y = 0; $y < min($height, 100); $y += 10) {
                $rgb = imagecolorat($image, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                $brightness += ($r + $g + $b) / 3;
                $pixelCount++;
            }
        }
        
        $avgBrightness = $brightness / $pixelCount;
        
        if ($avgBrightness < 50) {
            $textFound[] = 'Low light condition detected';
        } elseif ($avgBrightness > 200) {
            $textFound[] = 'Overexposed image detected';
        } else {
            $textFound[] = 'Good image quality for text extraction';
        }
        
        imagedestroy($image);
    }
    
    $textFound[] = 'Basic image analysis completed';
    
    return [
        'success' => true,
        'text_found' => $textFound
    ];
}

function validateEAN13Checksum($barcode) {
    if (strlen($barcode) != 13 || !ctype_digit($barcode)) {
        return false;
    }
    
    $sum = 0;
    for ($i = 0; $i < 12; $i++) {
        $digit = intval($barcode[$i]);
        $sum += ($i % 2 == 0) ? $digit : $digit * 3;
    }
    
    $checksum = (10 - ($sum % 10)) % 10;
    return $checksum == intval($barcode[12]);
}

function validateUPCAChecksum($barcode) {
    if (strlen($barcode) != 12 || !ctype_digit($barcode)) {
        return false;
    }
    
    $sum = 0;
    for ($i = 0; $i < 11; $i++) {
        $digit = intval($barcode[$i]);
        $sum += ($i % 2 == 0) ? $digit * 3 : $digit;
    }
    
    $checksum = (10 - ($sum % 10)) % 10;
    return $checksum == intval($barcode[11]);
}

function analyzeBarcodePattern($barcode) {
    $isSequential = true;
    $isRepetitive = true;
    $digitCounts = array_count_values(str_split($barcode));
    
    for ($i = 1; $i < strlen($barcode); $i++) {
        if (abs($barcode[$i] - $barcode[$i-1]) != 1) {
            $isSequential = false;
            break;
        }
    }
    
    $uniqueDigits = count($digitCounts);
    if ($uniqueDigits > 3) {
        $isRepetitive = false;
    }
    
    return [
        'is_sequential' => $isSequential,
        'is_repetitive' => $isRepetitive,
        'unique_digits' => $uniqueDigits,
        'digit_distribution' => $digitCounts
    ];
}

function checkImageDuplicates($imageHash) {
    require_once('db.php');
    $conn = connect();
    
    $sql = "SELECT COUNT(*) as count FROM medicine_verifications WHERE image_hash = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $imageHash);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $isDuplicate = $row['count'] > 0;
    
    $stmt->close();
    $conn->close();
    
    return [
        'is_duplicate' => $isDuplicate,
        'usage_count' => $row['count']
    ];
}

function checkBarcodeHistory($barcode, $medicineId) {
    require_once('db.php');
    $conn = connect();
    
    $sql = "SELECT COUNT(DISTINCT medicine_id) as medicine_count FROM medicine_verifications WHERE barcode_scanned = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $barcode);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $usedByMultiple = $row['medicine_count'] > 1;
    
    $stmt->close();
    $conn->close();
    
    return [
        'used_by_multiple_medicines' => $usedByMultiple,
        'medicine_count' => $row['medicine_count']
    ];
}

function analyzeManufacturerRisk($manufacturerId) {
    require_once('db.php');
    $conn = connect();
    
    $sql = "SELECT 
                COUNT(*) as total_reports,
                SUM(CASE WHEN verification_status = 'Verified Fake' THEN 1 ELSE 0 END) as counterfeit_count
            FROM reported_counterfeits rc
            WHERE rc.suspected_manufacturer IN (
                SELECT manufacturer_name FROM manufacturers WHERE manufacturer_id = ?
            )";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $manufacturerId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $counterfeitRate = 0;
    if ($row['total_reports'] > 0) {
        $counterfeitRate = ($row['counterfeit_count'] / $row['total_reports']) * 100;
    }
    
    $stmt->close();
    $conn->close();
    
    return [
        'high_risk' => $counterfeitRate > 20,
        'counterfeit_rate' => round($counterfeitRate, 2),
        'total_reports' => $row['total_reports']
    ];
}

function analyzeMedicineRisk($medicineName) {
    require_once('db.php');
    $conn = connect();
    
    $sql = "SELECT COUNT(*) as counterfeit_count 
            FROM reported_counterfeits 
            WHERE medicine_name = ? AND verification_status = 'Verified Fake'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $medicineName);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $counterfeitCount = $row['counterfeit_count'];
    
    $stmt->close();
    $conn->close();
    
    return [
        'frequently_counterfeited' => $counterfeitCount > 5,
        'counterfeit_count' => $counterfeitCount
    ];
}

function analyzeBatchPattern($batchNumber) {
    $suspicious = false;
    
    if (strlen($batchNumber) < 4) {
        $suspicious = true;
    }
    
    if (preg_match('/^(.)\1+$/', $batchNumber)) {
        $suspicious = true;
    }
    
    if (!preg_match('/[A-Z0-9]/', $batchNumber)) {
        $suspicious = true;
    }
    
    return [
        'suspicious' => $suspicious,
        'length' => strlen($batchNumber)
    ];
}

function findSimilarReports($medicineData, $reportHistory) {
    $matchCount = 0;
    
    foreach ($reportHistory as $report) {
        $similarity = 0;
        
        if (isset($medicineData['medicine_name']) && isset($report['medicine_name'])) {
            if ($medicineData['medicine_name'] == $report['medicine_name']) {
                $similarity += 40;
            }
        }
        
        if (isset($medicineData['barcode']) && isset($report['barcode'])) {
            if ($medicineData['barcode'] == $report['barcode']) {
                $similarity += 30;
            }
        }
        
        if (isset($medicineData['batch_number']) && isset($report['batch_number'])) {
            if ($medicineData['batch_number'] == $report['batch_number']) {
                $similarity += 30;
            }
        }
        
        if ($similarity >= 50) {
            $matchCount++;
        }
    }
    
    return [
        'match_found' => $matchCount > 0,
        'match_count' => $matchCount
    ];
}

function extractFeatures($medicineData, $userHistory) {
    $features = [
        'manufacturer_reputation' => 70,
        'barcode_validity' => 80,
        'price_consistency' => 75,
        'batch_pattern' => 85,
        'user_reliability' => 90,
        'medicine_history' => 80
    ];
    
    if (isset($medicineData['manufacturer_id'])) {
        $mfgRisk = analyzeManufacturerRisk($medicineData['manufacturer_id']);
        $features['manufacturer_reputation'] = max(0, 100 - $mfgRisk['counterfeit_rate']);
    }
    
    if (isset($medicineData['barcode'])) {
        $barcodeCheck = verifyBarcodeWithAI($medicineData['barcode']);
        $features['barcode_validity'] = $barcodeCheck['confidence'];
    }
    
    if (isset($medicineData['batch_number'])) {
        $batchCheck = analyzeBatchPattern($medicineData['batch_number']);
        $features['batch_pattern'] = $batchCheck['suspicious'] ? 30 : 90;
    }
    
    if (count($userHistory) > 0) {
        $genuineCount = 0;
        foreach ($userHistory as $record) {
            if (isset($record['verification_result']) && $record['verification_result'] == 'Genuine') {
                $genuineCount++;
            }
        }
        $features['user_reliability'] = min(100, ($genuineCount / count($userHistory)) * 100 + 20);
    }
    
    return $features;
}

function generateAIVerificationReport($medicineData, $imagePath = null) {
    $report = [
        'timestamp' => date('Y-m-d H:i:s'),
        'ai_version' => '1.0',
        'analysis_results' => []
    ];
    
    if ($imagePath && file_exists($imagePath)) {
        $imageAnalysis = analyzeImageAuthenticity($imagePath);
        $report['analysis_results']['image_verification'] = $imageAnalysis;
    }
    
    if (isset($medicineData['barcode'])) {
        $barcodeAnalysis = verifyBarcodeWithAI($medicineData['barcode'], $medicineData['medicine_id'] ?? null);
        $report['analysis_results']['barcode_verification'] = $barcodeAnalysis;
    }
    
    $counterfeitHistory = [];
    $counterfeitAnalysis = detectCounterfeitPatterns($medicineData, $counterfeitHistory);
    $report['analysis_results']['counterfeit_detection'] = $counterfeitAnalysis;
    
    $userHistory = [];
    $prediction = predictVerificationOutcome($medicineData, $userHistory);
    $report['analysis_results']['ai_prediction'] = $prediction;
    
    $overallConfidence = 0;
    $confidenceCount = 0;
    
    if (isset($imageAnalysis)) {
        $overallConfidence += $imageAnalysis['confidence'];
        $confidenceCount++;
    }
    if (isset($barcodeAnalysis)) {
        $overallConfidence += $barcodeAnalysis['confidence'];
        $confidenceCount++;
    }
    $overallConfidence += $counterfeitAnalysis['ai_confidence'];
    $confidenceCount++;
    $overallConfidence += $prediction['confidence'];
    $confidenceCount++;
    
    $report['overall_confidence'] = round($overallConfidence / $confidenceCount, 2);
    $report['final_verdict'] = $prediction['prediction'];
    
    return $report;
}
?>
