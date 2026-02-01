<?php

define('GEMINI_API_KEY', 'AIzaSyBOQWF8tMrPbq5_AHaDfBtMxZr3HDiYqRg');
define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent');
define('GEMINI_VISION_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro-vision:generateContent');

function analyzeImageWithGemini($imagePath) {
    if (!file_exists($imagePath)) {
        return [
            'success' => false,
            'error' => 'Image file not found'
        ];
    }
    
    $imageData = base64_encode(file_get_contents($imagePath));
    $mimeType = mime_content_type($imagePath);
    
    $prompt = "You are a pharmaceutical expert analyzing medicine packaging for authenticity. Analyze this medicine image and provide:
1. Medicine name if visible
2. Manufacturer name if visible
3. Batch number if visible
4. Expiry date if visible
5. Any text visible on packaging
6. Signs of counterfeit (poor print quality, spelling errors, missing holograms, irregular packaging)
7. Authenticity assessment (Genuine/Suspicious/Counterfeit) with confidence percentage
8. Specific concerns or red flags

Provide a detailed analysis in JSON format with keys: medicine_name, manufacturer, batch_number, expiry_date, visible_text, counterfeit_signs, authenticity_assessment, confidence_score, concerns";

    $requestBody = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt],
                    [
                        'inline_data' => [
                            'mime_type' => $mimeType,
                            'data' => $imageData
                        ]
                    ]
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.4,
            'topK' => 32,
            'topP' => 1,
            'maxOutputTokens' => 2048
        ]
    ];
    
    $response = makeGeminiRequest(GEMINI_VISION_URL, $requestBody);
    
    if ($response['success']) {
        $analysisText = $response['text'];
        
        $jsonStart = strpos($analysisText, '{');
        $jsonEnd = strrpos($analysisText, '}');
        
        if ($jsonStart !== false && $jsonEnd !== false) {
            $jsonStr = substr($analysisText, $jsonStart, $jsonEnd - $jsonStart + 1);
            $analysis = json_decode($jsonStr, true);
            
            if ($analysis) {
                return [
                    'success' => true,
                    'analysis' => $analysis,
                    'raw_response' => $analysisText
                ];
            }
        }
        
        return [
            'success' => true,
            'analysis' => [
                'medicine_name' => 'Not detected',
                'manufacturer' => 'Not detected',
                'authenticity_assessment' => 'Unknown',
                'confidence_score' => 0,
                'concerns' => [$analysisText]
            ],
            'raw_response' => $analysisText
        ];
    }
    
    return $response;
}

function extractTextFromImage($imagePath) {
    if (!file_exists($imagePath)) {
        return [
            'success' => false,
            'error' => 'Image file not found'
        ];
    }
    
    $imageData = base64_encode(file_get_contents($imagePath));
    $mimeType = mime_content_type($imagePath);
    
    $prompt = "Extract ALL visible text from this medicine packaging image. Include:
- Medicine name
- Generic name
- Manufacturer name
- Batch/Lot number
- Expiry date
- Manufacturing date
- Barcode number
- Any warning labels
- Dosage information
- Any other visible text

Format as a simple list.";

    $requestBody = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt],
                    [
                        'inline_data' => [
                            'mime_type' => $mimeType,
                            'data' => $imageData
                        ]
                    ]
                ]
            ]
        ]
    ];
    
    $response = makeGeminiRequest(GEMINI_VISION_URL, $requestBody);
    
    if ($response['success']) {
        return [
            'success' => true,
            'extracted_text' => $response['text'],
            'text_lines' => explode("\n", trim($response['text']))
        ];
    }
    
    return $response;
}

function detectCounterfeitWithAI($medicineData, $imageAnalysis = null) {
    $prompt = "You are an expert in pharmaceutical counterfeit detection. Analyze the following medicine data:\n\n";
    
    if (isset($medicineData['medicine_name'])) {
        $prompt .= "Medicine Name: " . $medicineData['medicine_name'] . "\n";
    }
    if (isset($medicineData['manufacturer'])) {
        $prompt .= "Manufacturer: " . $medicineData['manufacturer'] . "\n";
    }
    if (isset($medicineData['barcode'])) {
        $prompt .= "Barcode: " . $medicineData['barcode'] . "\n";
    }
    if (isset($medicineData['batch_number'])) {
        $prompt .= "Batch Number: " . $medicineData['batch_number'] . "\n";
    }
    if (isset($medicineData['price'])) {
        $prompt .= "Price: ₹" . $medicineData['price'] . "\n";
    }
    
    if ($imageAnalysis) {
        $prompt .= "\nImage Analysis Results:\n" . json_encode($imageAnalysis, JSON_PRETTY_PRINT) . "\n";
    }
    
    $prompt .= "\nProvide a comprehensive counterfeit risk assessment including:
1. Risk Level (LOW/MEDIUM/HIGH)
2. Risk Score (0-100)
3. Specific risk factors identified
4. Detailed recommendations
5. Action items for the user

Respond in JSON format with keys: risk_level, risk_score, risk_factors (array), recommendations (array), action_items (array), detailed_analysis";

    $requestBody = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.3,
            'maxOutputTokens' => 1024
        ]
    ];
    
    $response = makeGeminiRequest(GEMINI_API_URL, $requestBody);
    
    if ($response['success']) {
        $analysisText = $response['text'];
        
        $jsonStart = strpos($analysisText, '{');
        $jsonEnd = strrpos($analysisText, '}');
        
        if ($jsonStart !== false && $jsonEnd !== false) {
            $jsonStr = substr($analysisText, $jsonStart, $jsonEnd - $jsonStart + 1);
            $analysis = json_decode($jsonStr, true);
            
            if ($analysis) {
                return [
                    'success' => true,
                    'analysis' => $analysis
                ];
            }
        }
        
        return [
            'success' => true,
            'analysis' => [
                'risk_level' => 'MEDIUM',
                'risk_score' => 50,
                'risk_factors' => [],
                'recommendations' => [$analysisText],
                'action_items' => ['Consult pharmacist'],
                'detailed_analysis' => $analysisText
            ]
        ];
    }
    
    return $response;
}

function generateVerificationInsights($verificationData) {
    $prompt = "As a pharmaceutical safety AI, provide intelligent insights for this medicine verification:\n\n";
    $prompt .= json_encode($verificationData, JSON_PRETTY_PRINT);
    $prompt .= "\n\nGenerate:\n1. A user-friendly summary\n2. Safety recommendations\n3. What the user should do next\n4. Educational information about this medicine type\n\nKeep it concise and actionable.";

    $requestBody = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.7,
            'maxOutputTokens' => 512
        ]
    ];
    
    $response = makeGeminiRequest(GEMINI_API_URL, $requestBody);
    
    if ($response['success']) {
        return [
            'success' => true,
            'insights' => $response['text']
        ];
    }
    
    return $response;
}

function validateBarcodeWithAI($barcode) {
    $prompt = "Analyze this barcode number: $barcode\n\n";
    $prompt .= "Determine:\n1. Barcode type (EAN-13, UPC-A, etc.)\n2. Is the checksum valid?\n3. Country code (if applicable)\n4. Any suspicious patterns (sequential, repetitive, common fake patterns)\n5. Authenticity assessment\n\nRespond in JSON format with keys: barcode_type, is_valid, country_code, suspicious_patterns (array), authenticity, confidence_score";

    $requestBody = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.2,
            'maxOutputTokens' => 512
        ]
    ];
    
    $response = makeGeminiRequest(GEMINI_API_URL, $requestBody);
    
    if ($response['success']) {
        $analysisText = $response['text'];
        
        $jsonStart = strpos($analysisText, '{');
        $jsonEnd = strrpos($analysisText, '}');
        
        if ($jsonStart !== false && $jsonEnd !== false) {
            $jsonStr = substr($analysisText, $jsonStart, $jsonEnd - $jsonStart + 1);
            $analysis = json_decode($jsonStr, true);
            
            if ($analysis) {
                return [
                    'success' => true,
                    'analysis' => $analysis
                ];
            }
        }
    }
    
    return [
        'success' => false,
        'error' => 'Could not analyze barcode'
    ];
}

function makeGeminiRequest($url, $requestBody) {
    $apiUrl = $url . '?key=' . GEMINI_API_KEY;
    
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return [
            'success' => false,
            'error' => 'CURL Error: ' . $error
        ];
    }
    
    if ($httpCode !== 200) {
        return [
            'success' => false,
            'error' => 'API Error: HTTP ' . $httpCode,
            'response' => $response
        ];
    }
    
    $data = json_decode($response, true);
    
    if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
        return [
            'success' => true,
            'text' => $data['candidates'][0]['content']['parts'][0]['text'],
            'full_response' => $data
        ];
    }
    
    return [
        'success' => false,
        'error' => 'Unexpected API response format',
        'response' => $response
    ];
}

function generateComprehensiveAIReport($medicineData, $imagePath = null) {
    $report = [
        'timestamp' => date('Y-m-d H:i:s'),
        'ai_version' => 'Gemini Pro 1.0',
        'ai_type' => 'Google Generative AI',
        'analysis_results' => []
    ];
    
    if ($imagePath && file_exists($imagePath)) {
        $imageAnalysis = analyzeImageWithGemini($imagePath);
        if ($imageAnalysis['success']) {
            $report['analysis_results']['gemini_vision'] = $imageAnalysis['analysis'];
            
            $ocrResult = extractTextFromImage($imagePath);
            if ($ocrResult['success']) {
                $report['analysis_results']['ocr_extraction'] = $ocrResult;
            }
        } else {
            $report['analysis_results']['image_error'] = $imageAnalysis['error'];
        }
    }
    
    if (isset($medicineData['barcode']) && !empty($medicineData['barcode'])) {
        $barcodeAnalysis = validateBarcodeWithAI($medicineData['barcode']);
        if ($barcodeAnalysis['success']) {
            $report['analysis_results']['barcode_ai'] = $barcodeAnalysis['analysis'];
        }
    }
    
    $imageAnalysisData = isset($imageAnalysis['analysis']) ? $imageAnalysis['analysis'] : null;
    $counterfeitDetection = detectCounterfeitWithAI($medicineData, $imageAnalysisData);
    if ($counterfeitDetection['success']) {
        $report['analysis_results']['counterfeit_ai'] = $counterfeitDetection['analysis'];
    }
    
    $overallConfidence = 0;
    $confidenceCount = 0;
    
    if (isset($imageAnalysis['analysis']['confidence_score'])) {
        $overallConfidence += intval($imageAnalysis['analysis']['confidence_score']);
        $confidenceCount++;
    }
    if (isset($barcodeAnalysis['analysis']['confidence_score'])) {
        $overallConfidence += intval($barcodeAnalysis['analysis']['confidence_score']);
        $confidenceCount++;
    }
    if (isset($counterfeitDetection['analysis']['risk_score'])) {
        $overallConfidence += (100 - intval($counterfeitDetection['analysis']['risk_score']));
        $confidenceCount++;
    }
    
    $report['overall_confidence'] = $confidenceCount > 0 ? round($overallConfidence / $confidenceCount, 2) : 50;
    
    if (isset($imageAnalysis['analysis']['authenticity_assessment'])) {
        $report['final_verdict'] = $imageAnalysis['analysis']['authenticity_assessment'];
    } elseif (isset($counterfeitDetection['analysis']['risk_level'])) {
        $riskLevel = $counterfeitDetection['analysis']['risk_level'];
        $report['final_verdict'] = $riskLevel == 'LOW' ? 'Genuine' : ($riskLevel == 'HIGH' ? 'Counterfeit' : 'Suspicious');
    } else {
        $report['final_verdict'] = 'Unknown';
    }
    
    return $report;
}
?>
