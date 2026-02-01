<?php include '../Controllers/verify_medicine_session.php'; ?>
<?php
    require_once('../Models/medicineVerificationModel.php');
    require_once('../Models/medicineModel.php');
    require_once('../Models/geminiAI.php');
    
    if(!isset($_SESSION['user_id'])){
        header('location: ../Views/login.php');
        exit();
    }
    
    if(!isset($_REQUEST['id'])){
        header('location: ../Views/verify_medicine.php');
        exit();
    }
    
    $verification_id = $_REQUEST['id'];
    $verification = getVerificationById($verification_id);
    
    if(!$verification){
        header('location: ../Views/verify_medicine.php');
        exit();
    }
    
    // Get medicine details if found
    $medicine = null;
    if($medicine){
        $medicine = getMedicineById($verification['medicine_id']);
    }
    
    $aiReport = null;
    $aiEnabled = isset($_SESSION['ai_enabled']) && $_SESSION['ai_enabled'];
    if($verification['ai_analysis']){
        $aiReport = json_decode($verification['ai_analysis'], true);
    }
    
    // Determine result styling
    $result_class = 'card-blue';
    $result_icon = '❓';
    $result_message = 'Unknown Status';
    
    if($verification['verification_result'] == 'Genuine'){
        $result_class = 'card-green';
        $result_icon = '✅';
        $result_message = 'GENUINE MEDICINE';
    }else if($verification['verification_result'] == 'Counterfeit'){
        $result_class = 'card-orange';
        $result_icon = '❌';
        $result_message = 'COUNTERFEIT DETECTED';
    }else if($verification['verification_result'] == 'Suspicious'){
        $result_class = 'card-orange';
        $result_icon = '⚠️';
        $result_message = 'SUSPICIOUS - VERIFY FURTHER';
    }else if($verification['verification_result'] == 'Expired'){
        $result_class = 'card-orange';
        $result_icon = '⏰';
        $result_message = 'MEDICINE EXPIRED';
    }else if($verification['verification_result'] == 'Not Found'){
        $result_class = 'card-orange';
        $result_icon = '🔍';
        $result_message = 'NOT FOUND IN DATABASE';
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Result - MedVerify</title>
    <link rel="stylesheet" href="../Assets/professional.css">
    <link rel="stylesheet" href="../Assets/print.css" media="print">
</head>
<body id="top">
    <header>
        <center>
            <h1>MedVerify</h1>
            <p><b>Verification Result</b></p>
        </center>
    </header>

    <nav>
        <center>
            <ul>
                <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'){ ?>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="view_reports.php">View Reports</a></li>
                <?php } ?>
                <li><a href="verify_medicine.php">Verify Medicine</a></li>
                <li><a href="upload_report.php">Upload Report</a></li>
                <li><a href="calendar.php">Calendar</a></li>
                <li><a href="family_profile.php">Family Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </center>
    </nav>

    <hr>

    <main>
        <!-- Verification Result Card -->
        <table border="1" width="100%">
            <tr>
                <td align="center" class="<?php echo $result_class; ?>" style="padding: 30px;">
                    <h1 style="font-size: 60px; margin: 10px;"><?php echo $result_icon; ?></h1>
                    <h1><?php echo $result_message; ?></h1>
                    <?php if($aiEnabled){ ?>
                    <p style="font-size: 14px; color: #666;">🤖 AI-Powered Analysis</p>
                    <?php } ?>
                    <br>
                    <p style="font-size: 18px;"><b>Confidence Score: <?php echo $verification['confidence_score']; ?>%</b></p>
                    <br>
                    <p style="font-size: 16px;"><?php echo $verification['verification_notes']; ?></p>
                    <br>
                </td>
            </tr>
        </table>

        <br><br>

        <!-- AI Analysis Results -->
        <?php if($aiReport && isset($aiReport['analysis_results'])){ ?>
        <table width="100%">
            <tr>
                <td align="center">
                    <h3>🤖 Gemini Pro AI Analysis Report</h3>
                    <p><i>Powered by Google Generative AI - Real-time intelligent analysis</i></p>
                </td>
            </tr>
        </table>

        <table border="1" width="100%">
            <?php if(isset($aiReport['analysis_results']['gemini_vision'])){ 
                $visionAnalysis = $aiReport['analysis_results']['gemini_vision'];
            ?>
            <tr>
                <td colspan="2" style="background-color: #4285f4; color: white; padding: 10px; font-weight: bold;">
                    📷 Gemini Vision AI - Image Analysis
                </td>
            </tr>
            <?php if(isset($visionAnalysis['medicine_name'])){ ?>
            <tr>
                <td width="30%">AI Detected Medicine:</td>
                <td width="70%"><b><?php echo $visionAnalysis['medicine_name']; ?></b></td>
            </tr>
            <?php } ?>
            <?php if(isset($visionAnalysis['manufacturer'])){ ?>
            <tr>
                <td>AI Detected Manufacturer:</td>
                <td><?php echo $visionAnalysis['manufacturer']; ?></td>
            </tr>
            <?php } ?>
            <?php if(isset($visionAnalysis['batch_number'])){ ?>
            <tr>
                <td>AI Detected Batch Number:</td>
                <td><?php echo $visionAnalysis['batch_number']; ?></td>
            </tr>
            <?php } ?>
            <?php if(isset($visionAnalysis['expiry_date'])){ ?>
            <tr>
                <td>AI Detected Expiry:</td>
                <td><?php echo $visionAnalysis['expiry_date']; ?></td>
            </tr>
            <?php } ?>
            <?php if(isset($visionAnalysis['authenticity_assessment'])){ ?>
            <tr>
                <td>Gemini Assessment:</td>
                <td style="font-weight: bold; font-size: 16px; color: <?php echo $visionAnalysis['authenticity_assessment'] == 'Genuine' ? 'green' : ($visionAnalysis['authenticity_assessment'] == 'Counterfeit' ? 'red' : 'orange'); ?>;">
                    <?php echo $visionAnalysis['authenticity_assessment']; ?> 
                    <?php if(isset($visionAnalysis['confidence_score'])){ ?>
                        (<?php echo $visionAnalysis['confidence_score']; ?>% confidence)
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
            <?php if(isset($visionAnalysis['counterfeit_signs']) && is_array($visionAnalysis['counterfeit_signs']) && count($visionAnalysis['counterfeit_signs']) > 0){ ?>
            <tr>
                <td>Counterfeit Signs Detected:</td>
                <td style="color: red;">
                    <ul style="margin: 5px 0;">
                    <?php foreach($visionAnalysis['counterfeit_signs'] as $sign){ ?>
                        <li><?php echo $sign; ?></li>
                    <?php } ?>
                    </ul>
                </td>
            </tr>
            <?php } ?>
            <?php if(isset($visionAnalysis['concerns']) && is_array($visionAnalysis['concerns']) && count($visionAnalysis['concerns']) > 0){ ?>
            <tr>
                <td>AI Concerns:</td>
                <td style="color: orange;">
                    <ul style="margin: 5px 0;">
                    <?php foreach($visionAnalysis['concerns'] as $concern){ ?>
                        <li><?php echo $concern; ?></li>
                    <?php } ?>
                    </ul>
                </td>
            </tr>
            <?php } ?>
            <?php } ?>

            <?php if(isset($aiReport['analysis_results']['ocr_extraction'])){ 
                $ocrResult = $aiReport['analysis_results']['ocr_extraction'];
            ?>
            <tr>
                <td colspan="2" style="background-color: #4285f4; color: white; padding: 10px; font-weight: bold;">
                    📝 Gemini OCR - Text Extraction
                </td>
            </tr>
            <tr>
                <td>Extracted Text:</td>
                <td>
                    <div style="background-color: #f5f5f5; padding: 10px; font-family: monospace; max-height: 200px; overflow-y: auto;">
                        <?php echo nl2br(htmlspecialchars($ocrResult['extracted_text'])); ?>
                    </div>
                </td>
            </tr>
            <?php } ?>

            <?php if(isset($aiReport['analysis_results']['barcode_ai'])){ 
                $barcodeAI = $aiReport['analysis_results']['barcode_ai'];
            ?>
            <tr>
                <td colspan="2" style="background-color: #4285f4; color: white; padding: 10px; font-weight: bold;">
                    🔢 Gemini Barcode Analysis
                </td>
            </tr>
            <?php if(isset($barcodeAI['barcode_type'])){ ?>
            <tr>
                <td>Barcode Type:</td>
                <td><?php echo $barcodeAI['barcode_type']; ?></td>
            </tr>
            <?php } ?>
            <?php if(isset($barcodeAI['is_valid'])){ ?>
            <tr>
                <td>Checksum Validation:</td>
                <td style="color: <?php echo $barcodeAI['is_valid'] ? 'green' : 'red'; ?>; font-weight: bold;">
                    <?php echo $barcodeAI['is_valid'] ? '✅ Valid' : '❌ Invalid'; ?>
                </td>
            </tr>
            <?php } ?>
            <?php if(isset($barcodeAI['country_code'])){ ?>
            <tr>
                <td>Country Code:</td>
                <td><?php echo $barcodeAI['country_code']; ?></td>
            </tr>
            <?php } ?>
            <?php if(isset($barcodeAI['suspicious_patterns']) && is_array($barcodeAI['suspicious_patterns']) && count($barcodeAI['suspicious_patterns']) > 0){ ?>
            <tr>
                <td>Suspicious Patterns:</td>
                <td style="color: orange;">
                    <ul style="margin: 5px 0;">
                    <?php foreach($barcodeAI['suspicious_patterns'] as $pattern){ ?>
                        <li><?php echo $pattern; ?></li>
                    <?php } ?>
                    </ul>
                </td>
            </tr>
            <?php } ?>
            <?php if(isset($barcodeAI['authenticity'])){ ?>
            <tr>
                <td>Barcode Authenticity:</td>
                <td style="font-weight: bold;">
                    <?php echo $barcodeAI['authenticity']; ?>
                    <?php if(isset($barcodeAI['confidence_score'])){ ?>
                        (<?php echo $barcodeAI['confidence_score']; ?>% confidence)
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
            <?php } ?>

            <?php if(isset($aiReport['analysis_results']['counterfeit_ai'])){ 
                $counterfeitAI = $aiReport['analysis_results']['counterfeit_ai'];
            ?>
            <tr>
                <td colspan="2" style="background-color: #4285f4; color: white; padding: 10px; font-weight: bold;">
                    🚨 Gemini Counterfeit Detection AI
                </td>
            </tr>
            <?php if(isset($counterfeitAI['risk_level'])){ ?>
            <tr>
                <td>AI Risk Assessment:</td>
                <td style="color: <?php echo $counterfeitAI['risk_level'] == 'HIGH' ? 'red' : ($counterfeitAI['risk_level'] == 'MEDIUM' ? 'orange' : 'green'); ?>; font-weight: bold; font-size: 18px;">
                    <?php echo $counterfeitAI['risk_level']; ?>
                    <?php if(isset($counterfeitAI['risk_score'])){ ?>
                        RISK (<?php echo $counterfeitAI['risk_score']; ?>/100)
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
            <?php if(isset($counterfeitAI['risk_factors']) && is_array($counterfeitAI['risk_factors']) && count($counterfeitAI['risk_factors']) > 0){ ?>
            <tr>
                <td>AI Identified Risk Factors:</td>
                <td>
                    <ul style="margin: 5px 0;">
                    <?php foreach($counterfeitAI['risk_factors'] as $factor){ ?>
                        <li><?php echo $factor; ?></li>
                    <?php } ?>
                    </ul>
                </td>
            </tr>
            <?php } ?>
            <?php if(isset($counterfeitAI['recommendations']) && is_array($counterfeitAI['recommendations']) && count($counterfeitAI['recommendations']) > 0){ ?>
            <tr>
                <td>Gemini Recommendations:</td>
                <td style="background-color: #e6f7ff;">
                    <ul style="margin: 5px 0;">
                    <?php foreach($counterfeitAI['recommendations'] as $rec){ ?>
                        <li><b><?php echo $rec; ?></b></li>
                    <?php } ?>
                    </ul>
                </td>
            </tr>
            <?php } ?>
            <?php if(isset($counterfeitAI['action_items']) && is_array($counterfeitAI['action_items']) && count($counterfeitAI['action_items']) > 0){ ?>
            <tr>
                <td>Action Items:</td>
                <td>
                    <ol style="margin: 5px 0;">
                    <?php foreach($counterfeitAI['action_items'] as $action){ ?>
                        <li><?php echo $action; ?></li>
                    <?php } ?>
                    </ol>
                </td>
            </tr>
            <?php } ?>
            <?php if(isset($counterfeitAI['detailed_analysis'])){ ?>
            <tr>
                <td>Detailed AI Analysis:</td>
                <td style="background-color: #f9f9f9; padding: 10px;">
                    <small><?php echo nl2br(htmlspecialchars($counterfeitAI['detailed_analysis'])); ?></small>
                </td>
            </tr>
            <?php } ?>
            <?php } ?>

            <tr>
                <td colspan="2" style="background-color: #34a853; color: white; padding: 10px; text-align: center; font-weight: bold;">
                    <small>⚡ Powered by Google Gemini Pro AI | Version: <?php echo $aiReport['ai_version']; ?> | Analysis Time: <?php echo $aiReport['timestamp']; ?></small>
                </td>
            </tr>
        </table>

        <br><br>
        <?php } ?>

        <!-- Medicine Details (if found) -->
        <?php if($medicine){ ?>
        <table width="100%">
            <tr>
                <td align="center">
                    <h3>💊 Medicine Information</h3>
                </td>
            </tr>
        </table>

        <table border="1" width="100%">
            <tr>
                <td width="30%"><b>Medicine Name:</b></td>
                <td width="70%"><?php echo $medicine['medicine_name']; ?></td>
            </tr>
            <tr>
                <td><b>Generic Name:</b></td>
                <td><?php echo $medicine['generic_name']; ?></td>
            </tr>
            <tr>
                <td><b>Manufacturer:</b></td>
                <td><?php echo $medicine['manufacturer_name']; ?> (<?php echo $medicine['country']; ?>)</td>
            </tr>
            <tr>
                <td><b>Category:</b></td>
                <td><?php echo $medicine['category']; ?></td>
            </tr>
            <tr>
                <td><b>Dosage Form:</b></td>
                <td><?php echo $medicine['dosage_form']; ?> - <?php echo $medicine['strength']; ?></td>
            </tr>
            <tr>
                <td><b>Batch Number:</b></td>
                <td><?php echo $medicine['batch_number']; ?></td>
            </tr>
            <tr>
                <td><b>Manufacturing Date:</b></td>
                <td><?php echo date('M d, Y', strtotime($medicine['manufacturing_date'])); ?></td>
            </tr>
            <tr>
                <td><b>Expiry Date:</b></td>
                <td style="<?php echo ($verification['expiry_check'] == 'Expired') ? 'color: red; font-weight: bold;' : ''; ?>">
                    <?php echo date('M d, Y', strtotime($medicine['expiry_date'])); ?>
                    <?php if($verification['expiry_check'] == 'Expired'){ echo ' - EXPIRED!'; } ?>
                    <?php if($verification['expiry_check'] == 'Near Expiry'){ echo ' - Expiring Soon'; } ?>
                </td>
            </tr>
            <tr>
                <td><b>MRP:</b></td>
                <td>₹<?php echo number_format($medicine['mrp'], 2); ?></td>
            </tr>
            <tr>
                <td><b>Description:</b></td>
                <td><?php echo $medicine['description']; ?></td>
            </tr>
            <tr>
                <td><b>Prescription Required:</b></td>
                <td><?php echo $medicine['prescription_required']; ?></td>
            </tr>
        </table>

        <br><br>
        <?php } ?>

        <!-- Verification Details -->
        <table width="100%">
            <tr>
                <td align="center">
                    <h3>📋 Verification Details</h3>
                </td>
            </tr>
        </table>

        <table border="1" width="100%">
            <tr>
                <td width="30%"><b>Verification ID:</b></td>
                <td width="70%">#<?php echo $verification['verification_id']; ?></td>
            </tr>
            <tr>
                <td><b>Verification Method:</b></td>
                <td><?php echo $verification['verification_method']; ?></td>
            </tr>
            <tr>
                <td><b>Barcode Scanned:</b></td>
                <td><?php echo $verification['barcode_scanned'] ? $verification['barcode_scanned'] : 'N/A'; ?></td>
            </tr>
            <tr>
                <td><b>Batch Number Entered:</b></td>
                <td><?php echo $verification['batch_number_entered'] ? $verification['batch_number_entered'] : 'N/A'; ?></td>
            </tr>
            <tr>
                <td><b>Expiry Check:</b></td>
                <td style="<?php echo ($verification['expiry_check'] == 'Expired') ? 'color: red; font-weight: bold;' : ''; ?>">
                    <?php echo $verification['expiry_check']; ?>
                </td>
            </tr>
            <tr>
                <td><b>Manufacturer Match:</b></td>
                <td style="<?php echo ($verification['manufacturer_match'] == 'Mismatch') ? 'color: red;' : ''; ?>">
                    <?php echo $verification['manufacturer_match']; ?>
                </td>
            </tr>
            <tr>
                <td><b>Batch Match:</b></td>
                <td style="<?php echo ($verification['batch_match'] == 'Mismatch') ? 'color: red;' : ''; ?>">
                    <?php echo $verification['batch_match']; ?>
                </td>
            </tr>
            <tr>
                <td><b>Verified At:</b></td>
                <td><?php echo date('M d, Y - H:i:s', strtotime($verification['verified_at'])); ?></td>
            </tr>
        </table>

        <br><br>

        <!-- Action Buttons -->
        <table width="100%">
            <tr>
                <td align="center">
                    <?php if($verification['verification_result'] == 'Counterfeit' || $verification['verification_result'] == 'Suspicious'){ ?>
                    <a href="report_counterfeit.php"><button style="background-color: red; color: white; padding: 15px 30px; font-weight: bold;">🚨 Report This Counterfeit</button></a>
                    <?php } ?>
                    <a href="verify_medicine.php"><button style="background-color: lightgreen; padding: 15px 30px; font-weight: bold;">🔍 Verify Another Medicine</button></a>
                    <a href="dashboard.php"><button>📊 Go to Dashboard</button></a>
                    <button onclick="window.print()">🖨️ Print Result</button>
                </td>
            </tr>
        </table>

        <br>

        <!-- Recommendations -->
        <?php if($verification['verification_result'] == 'Counterfeit' || $verification['verification_result'] == 'Suspicious'){ ?>
        <table width="100%" style="background-color: #ffe6e6; border: 2px solid red;">
            <tr>
                <td align="center" style="padding: 20px;">
                    <h3 style="color: red;">⚠️ WARNING - DO NOT USE THIS MEDICINE!</h3>
                    <p><b>Recommendations:</b></p>
                    <ul style="text-align: left; display: inline-block;">
                        <li>Do not consume this medicine</li>
                        <li><a href="report_counterfeit.php" style="color: red; font-weight: bold;">Report this counterfeit medicine immediately</a></li>
                        <li>Report to nearest pharmacy or health authority</li>
                        <li>Contact the manufacturer directly</li>
                        <li>Consult a doctor if already consumed</li>
                    </ul>
                </td>
            </tr>
        </table>
        <br>
        <?php } ?>

        <!-- Back to Top -->
        <table width="100%">
            <tr>
                <td align="center">
                    <a href="#top">Back to Top</a>
                </td>
            </tr>
        </table>
    </main>

    <hr>

    <footer>
        <center>
            <p>&copy; 2025 MedVerify | Your Health, Our Priority</p>
        </center>
    </footer>
</body>
</html>
