<?php include '../Controllers/verify_medicine_session.php'; ?>
<?php
    require_once('../Models/medicineVerificationModel.php');
    
    if(!isset($_SESSION['user_id'])){
        header('location: ../Views/login.php');
        exit();
    }
    
    $user_id = $_SESSION['user_id'];
    
    // Get user statistics
    $stats = getUserVerificationStats($user_id);
    $today_count = getTodayVerificationCount($user_id);
    $recent_verifications = getRecentVerifications(5, $user_id);
    
    // Error/Success messages
    $error = "";
    if(isset($_SESSION['error'])){
        $error = $_SESSION['error'];
        unset($_SESSION['error']);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Medicine - MedVerify</title>
    <link rel="stylesheet" href="../Assets/professional.css">
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script src="../Assets/validate_medicine.js"></script>
    <style>
        #barcode-scanner {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            border: 3px solid var(--secondary-color);
            border-radius: var(--radius-lg);
            overflow: hidden;
            display: none;
            background: white;
            box-shadow: var(--shadow-xl);
        }
        #scanner-region {
            position: relative;
            min-height: 300px;
        }
        .scanner-controls {
            padding: var(--spacing);
            background: var(--gray-50);
            text-align: center;
        }
        .scanner-status {
            padding: var(--spacing);
            background: var(--info-bg);
            text-align: center;
            font-weight: 600;
            color: var(--info-text);
        }
        #scanner-result {
            padding: var(--spacing);
            background: var(--success-bg);
            color: var(--success-text);
            font-weight: 600;
            text-align: center;
            display: none;
        }
        .scan-button {
            background: var(--secondary-color);
            color: white;
            padding: var(--spacing) var(--spacing-lg);
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
        }
        .scan-button:hover {
            background: var(--secondary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        .stop-button {
            background: var(--accent-danger);
            color: white;
            padding: var(--spacing-sm) var(--spacing);
            font-weight: 600;
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
        }
    </style>
</head>
<body id="top">
    <header>
        <div class="text-center">
            <h1>🏥 MedVerify</h1>
            <p>Medicine Authentication System</p>
        </div>
    </header>

    <nav>
        <ul>
            <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'){ ?>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="verification_history.php">History</a></li>
            <li><a href="manage_medicines.php">Medicines</a></li>
            <li><a href="manage_manufacturers.php">Manufacturers</a></li>
            <li><a href="analytics.php">Analytics</a></li>
            <li><a href="review_counterfeits.php">Reports</a></li>
            <li><a href="view_reports.php">View Reports</a></li>
            <?php } ?>
            <li><a href="verify_medicine.php" style="color: var(--primary-color);">Verify Medicine</a></li>
            <li><a href="report_counterfeit.php">Report</a></li>
            <li><a href="upload_report.php">Upload</a></li>
            <li><a href="calendar.php">Calendar</a></li>
            <li><a href="family_profile.php">Family</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <main>
        <div class="fade-in">
            <h2 class="section-title">🔍 Verify Medicine Authenticity</h2>
            <p style="text-align: center; color: var(--gray-600); margin-bottom: 2rem;">Check if your medicine is genuine or counterfeit using AI-powered analysis</p>

            <?php if($error != ""){ ?>
            <div class="alert alert-error">
                ✕ <?php echo $error; ?>
            </div>
            <?php } ?>

            <!-- Statistics Cards -->
            <div class="grid grid-3" style="margin-bottom: 2rem;">
                <td align="center" class="card-blue" width="33%">
                    <h3>Today's Verifications</h3>
                    <br>
                    <h1><?php echo $today_count; ?></h1>
                    <p>Scans Today</p>
                    <br>
                </td>
                <td align="center" class="card-green" width="33%">
                    <h3>Genuine Medicines</h3>
                    <br>
                    <h1><?php echo $stats['genuine_count']; ?></h1>
                    <p>Verified Authentic</p>
                    <br>
                </td>
                <td align="center" class="card-orange" width="34%">
                    <h3>Total Verifications</h3>
                    <br>
                    <h1><?php echo $stats['total_verifications']; ?></h1>
                    <p>All Time</p>
                    <br>
                </td>
            </tr>
        </table>

        <br><br>

        <!-- Verification Form -->
        <form action="../Controllers/verify_medicine_ai.php" method="post" enctype="multipart/form-data" onsubmit="return validateMedicineForm()">
        <table width="100%">
            <tr>
                <td align="center">
                    <h3>🔎 AI-Powered Medicine Verification</h3>
                    <p><i>Upload image, scan barcode, or enter details for AI analysis</i></p>
                </td>
            </tr>
        </table>

        <!-- Barcode Scanner Section -->
        <table width="100%">
            <tr>
                <td align="center">
                    <button type="button" class="scan-button" id="start-scan-btn" onclick="startBarcodeScanner()">
                        📷 Scan Barcode with Camera
                    </button>
                </td>
            </tr>
        </table>

        <br>

        <!-- Scanner Container -->
        <div id="barcode-scanner">
            <div class="scanner-status" id="scanner-status">
                📷 Camera is starting... Please allow camera access
            </div>
            <div id="scanner-region"></div>
            <div id="scanner-result"></div>
            <div class="scanner-controls">
                <button type="button" class="stop-button" onclick="stopBarcodeScanner()">
                    ⏹️ Stop Scanner
                </button>
                <br><br>
                <small>Position the barcode within the camera frame</small>
            </div>
        </div>

        <br>

        <table width="100%">
            <tr>
                <td align="center">
                    <h3>📝 Manual Entry</h3>
                </td>
            </tr>
        </table>

        <table border="1" width="100%">
            <tr>
                <td width="30%">Verification Method:</td>
                <td width="70%">
                    <select name="method" id="verification_method" style="width: 100%">
                        <option value="AI Image Analysis">AI Image Analysis</option>
                        <option value="Barcode Scan">Barcode Scan</option>
                        <option value="Manual Entry">Manual Entry</option>
                        <option value="QR Code">QR Code</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Medicine Image (AI Analysis):</td>
                <td>
                    <input type="file" name="medicine_image" id="medicine_image" accept="image/*" style="width: 100%">
                    <br><small style="color: #666;">Upload medicine photo for AI authenticity analysis</small>
                </td>
            </tr>
            <tr>
                <td>Barcode Number:</td>
                <td><input type="text" name="barcode" id="barcode_scanned" placeholder="Enter barcode or use scanner above" style="width: 100%"></td>
            </tr>
            <tr>
                <td>Batch Number:</td>
                <td><input type="text" name="batch_number" id="batch_number_entered" placeholder="Enter batch number (e.g., BATCH001)" style="width: 100%"></td>
            </tr>
            <tr>
                <td colspan="2" align="center" style="background-color: #e6f7ff; padding: 10px;">
                    <small><i>🤖 AI will analyze image, barcode pattern, and historical data for counterfeit detection</i></small>
                </td>
            </tr>
        </table>

        <br>

        <table width="100%">
            <tr>
                <td align="center">
                    <input type="submit" name="submit" value="🔍 Verify Medicine" style="background-color: lightgreen; padding: 15px 30px; font-size: 16px; font-weight: bold;">
                    <input type="reset" value="Clear Form">
                </td>
            </tr>
        </table>
        </form>

        <br><br>

        <!-- How It Works Section -->
        <table width="100%">
            <tr>
                <td align="center">
                    <h3>📋 How Medicine Verification Works</h3>
                </td>
            </tr>
        </table>

        <table border="1" width="100%">
            <tr>
                <td width="25%" align="center" style="background-color: lightblue; padding: 15px;">
                    <b>1. Enter Details</b><br>
                    <small>Input barcode or batch number</small>
                </td>
                <td width="25%" align="center" style="background-color: lightgreen; padding: 15px;">
                    <b>2. Database Check</b><br>
                    <small>System searches our database</small>
                </td>
                <td width="25%" align="center" style="background-color: lightyellow; padding: 15px;">
                    <b>3. AI Analysis</b><br>
                    <small>Verify authenticity & expiry</small>
                </td>
                <td width="25%" align="center" style="background-color: lightcoral; padding: 15px;">
                    <b>4. Get Result</b><br>
                    <small>Genuine or Counterfeit</small>
                </td>
            </tr>
        </table>

        <br><br>

        <!-- Recent Verifications -->
        <table width="100%">
            <tr>
                <td align="center">
                    <h3>📊 Your Recent Verifications</h3>
                </td>
            </tr>
        </table>

        <table border="1" width="100%">
            <tr>
                <th>Date & Time</th>
                <th>Medicine Name</th>
                <th>Barcode</th>
                <th>Result</th>
            </tr>
            <?php
            if(count($recent_verifications) > 0){
                foreach($recent_verifications as $verify){
                    $result_color = 'black';
                    if($verify['verification_result'] == 'Genuine'){
                        $result_color = 'green';
                    }else if($verify['verification_result'] == 'Counterfeit'){
                        $result_color = 'red';
                    }else if($verify['verification_result'] == 'Suspicious'){
                        $result_color = 'orange';
                    }
            ?>
            <tr>
                <td><?php echo date('M d, Y H:i', strtotime($verify['verified_at'])); ?></td>
                <td><?php echo $verify['medicine_name'] ? $verify['medicine_name'] : 'Unknown'; ?></td>
                <td><?php echo $verify['barcode_scanned']; ?></td>
                <td style="color: <?php echo $result_color; ?>; font-weight: bold;">
                    <?php echo $verify['verification_result']; ?>
                </td>
            </tr>
            <?php
                }
            }else{
            ?>
            <tr>
                <td colspan="4" align="center">No verifications yet. Start by verifying a medicine above!</td>
            </tr>
            <?php
            }
            ?>
        </table>

        <br>

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
            <p>&copy; 2025 MedVerify | Protecting Your Health</p>
        </center>
    </footer>

    <!-- Barcode Scanner Script -->
    <script src="../Assets/barcode_scanner.js"></script>
</body>
</html>
