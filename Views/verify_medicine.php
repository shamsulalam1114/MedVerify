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
    <link rel="stylesheet" href="../Assets/dashboard.css">
    <script src="../Assets/validate_medicine.js"></script>
</head>
<body id="top">
    <header>
        <center>
            <h1>MedVerify</h1>
            <p><b>Medicine Authentication System</b></p>
        </center>
    </header>

    <nav>
        <center>
            <ul>
                <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'){ ?>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="verification_history.php">Verification History</a></li>
                <li><a href="manage_medicines.php">Manage Medicines</a></li>
                <li><a href="view_reports.php">View Reports</a></li>
                <?php } ?>
                <li><a href="verify_medicine.php"><b>Verify Medicine</b></a></li>
                <li><a href="upload_report.php">Upload Report</a></li>
                <li><a href="calendar.php">Calendar</a></li>
                <li><a href="family_profile.php">Family Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </center>
    </nav>

    <hr>

    <main>
        <!-- Title -->
        <table width="100%">
            <tr>
                <td align="center">
                    <h2>🔍 Verify Medicine Authenticity</h2>
                    <p><i>Check if your medicine is genuine or counterfeit</i></p>
                </td>
            </tr>
        </table>

        <br>

        <!-- Error Message -->
        <?php if($error != ""){ ?>
        <table width="100%">
            <tr>
                <td align="center">
                    <p style="color: red; font-weight: bold;"><?php echo $error; ?></p>
                </td>
            </tr>
        </table>
        <br>
        <?php } ?>

        <!-- Statistics Cards -->
        <table border="1" width="100%">
            <tr>
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
        <form action="../Controllers/verify_medicine.php" method="post" enctype="" onsubmit="return validateMedicineForm()">
        <table width="100%">
            <tr>
                <td align="center">
                    <h3>🔎 Enter Medicine Details</h3>
                </td>
            </tr>
        </table>

        <table border="1" width="100%">
            <tr>
                <td width="30%">Verification Method:</td>
                <td width="70%">
                    <select name="verification_method" style="width: 100%">
                        <option value="Manual">Manual Entry</option>
                        <option value="Barcode">Barcode Scan</option>
                        <option value="QR Code">QR Code</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Barcode Number:</td>
                <td><input type="text" name="barcode_scanned" placeholder="Enter barcode (e.g., 8901234567890)" style="width: 100%"></td>
            </tr>
            <tr>
                <td>Batch Number:</td>
                <td><input type="text" name="batch_number_entered" placeholder="Enter batch number (e.g., BATCH001)" style="width: 100%"></td>
            </tr>
            <tr>
                <td colspan="2" align="center" style="background-color: lightyellow;">
                    <small><i>💡 Enter either Barcode OR Batch Number (or both for better accuracy)</i></small>
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
</body>
</html>
