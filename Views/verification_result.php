<?php include '../Controllers/verify_medicine_session.php'; ?>
<?php
    require_once('../Models/medicineVerificationModel.php');
    require_once('../Models/medicineModel.php');
    require_once('../Models/aiModel.php');
    
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
    <link rel="stylesheet" href="../Assets/dashboard.css">
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
                    <h3>🤖 AI Analysis Report</h3>
                    <p><i>Advanced machine learning analysis completed</i></p>
                </td>
            </tr>
        </table>

        <table border="1" width="100%">
            <?php if(isset($aiReport['analysis_results']['image_verification'])){ 
                $imgAnalysis = $aiReport['analysis_results']['image_verification'];
            ?>
            <tr>
                <td colspan="2" style="background-color: #e6f7ff; padding: 10px;">
                    <b>📷 Image Authenticity Analysis</b>
                </td>
            </tr>
            <tr>
                <td width="30%">Authenticity Score:</td>
                <td width="70%"><?php echo $imgAnalysis['authenticity_score']; ?>%</td>
            </tr>
            <tr>
                <td>Features Detected:</td>
                <td>
                    <ul style="margin: 5px 0;">
                    <?php foreach($imgAnalysis['features_detected'] as $feature){ ?>
                        <li><?php echo $feature; ?></li>
                    <?php } ?>
                    </ul>
                </td>
            </tr>
            <?php if(count($imgAnalysis['warnings']) > 0){ ?>
            <tr>
                <td>AI Warnings:</td>
                <td style="color: orange;">
                    <ul style="margin: 5px 0;">
                    <?php foreach($imgAnalysis['warnings'] as $warning){ ?>
                        <li><?php echo $warning; ?></li>
                    <?php } ?>
                    </ul>
                </td>
            </tr>
            <?php } ?>
            <?php } ?>

            <?php if(isset($aiReport['analysis_results']['barcode_verification'])){ 
                $barcodeAnalysis = $aiReport['analysis_results']['barcode_verification'];
            ?>
            <tr>
                <td colspan="2" style="background-color: #e6f7ff; padding: 10px;">
                    <b>🔢 Barcode AI Validation</b>
                </td>
            </tr>
            <tr>
                <td>Barcode Type:</td>
                <td><?php echo $barcodeAnalysis['barcode_type']; ?></td>
            </tr>
            <tr>
                <td>Validity:</td>
                <td style="color: <?php echo $barcodeAnalysis['is_valid'] ? 'green' : 'red'; ?>; font-weight: bold;">
                    <?php echo $barcodeAnalysis['is_valid'] ? '✅ Valid' : '❌ Invalid'; ?>
                </td>
            </tr>
            <tr>
                <td>AI Confidence:</td>
                <td><?php echo $barcodeAnalysis['confidence']; ?>%</td>
            </tr>
            <?php if(count($barcodeAnalysis['warnings']) > 0){ ?>
            <tr>
                <td>Pattern Warnings:</td>
                <td style="color: orange;">
                    <ul style="margin: 5px 0;">
                    <?php foreach($barcodeAnalysis['warnings'] as $warning){ ?>
                        <li><?php echo $warning; ?></li>
                    <?php } ?>
                    </ul>
                </td>
            </tr>
            <?php } ?>
            <?php } ?>

            <?php if(isset($aiReport['analysis_results']['counterfeit_detection'])){ 
                $counterfeitAI = $aiReport['analysis_results']['counterfeit_detection'];
            ?>
            <tr>
                <td colspan="2" style="background-color: #e6f7ff; padding: 10px;">
                    <b>🚨 Counterfeit Pattern Detection</b>
                </td>
            </tr>
            <tr>
                <td>Risk Level:</td>
                <td style="color: <?php echo $counterfeitAI['risk_level'] == 'HIGH' ? 'red' : ($counterfeitAI['risk_level'] == 'MEDIUM' ? 'orange' : 'green'); ?>; font-weight: bold; font-size: 16px;">
                    <?php echo $counterfeitAI['risk_level']; ?> (<?php echo $counterfeitAI['risk_score']; ?>%)
                </td>
            </tr>
            <?php if(count($counterfeitAI['risk_factors']) > 0){ ?>
            <tr>
                <td>Risk Factors Identified:</td>
                <td>
                    <ul style="margin: 5px 0;">
                    <?php foreach($counterfeitAI['risk_factors'] as $factor){ ?>
                        <li><?php echo $factor; ?></li>
                    <?php } ?>
                    </ul>
                </td>
            </tr>
            <?php } ?>
            <tr>
                <td>AI Recommendations:</td>
                <td>
                    <ul style="margin: 5px 0;">
                    <?php foreach($counterfeitAI['recommendations'] as $rec){ ?>
                        <li><?php echo $rec; ?></li>
                    <?php } ?>
                    </ul>
                </td>
            </tr>
            <?php } ?>

            <?php if(isset($aiReport['analysis_results']['ai_prediction'])){ 
                $prediction = $aiReport['analysis_results']['ai_prediction'];
            ?>
            <tr>
                <td colspan="2" style="background-color: #e6f7ff; padding: 10px;">
                    <b>🎯 AI Prediction Model</b>
                </td>
            </tr>
            <tr>
                <td>Predicted Outcome:</td>
                <td style="font-weight: bold; font-size: 16px;">
                    <?php echo $prediction['prediction']; ?> (<?php echo $prediction['confidence']; ?>% confidence)
                </td>
            </tr>
            <tr>
                <td>Genuine Probability:</td>
                <td>
                    <div style="background-color: #f0f0f0; border-radius: 10px; height: 25px; position: relative;">
                        <div style="background-color: <?php echo $prediction['genuine_probability'] > 75 ? '#4CAF50' : ($prediction['genuine_probability'] > 50 ? 'orange' : 'red'); ?>; width: <?php echo $prediction['genuine_probability']; ?>%; height: 100%; border-radius: 10px; position: relative;">
                            <span style="position: absolute; right: 10px; line-height: 25px; color: white; font-weight: bold;"><?php echo $prediction['genuine_probability']; ?>%</span>
                        </div>
                    </div>
                </td>
            </tr>
            <?php } ?>

            <tr>
                <td colspan="2" style="background-color: #f0f0f0; padding: 10px; text-align: center;">
                    <small><i>AI Version: <?php echo $aiReport['ai_version']; ?> | Analysis Time: <?php echo $aiReport['timestamp']; ?></i></small>
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
