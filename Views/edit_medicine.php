<?php include '../Controllers/manage_medicines_session.php'; ?>
<?php
    require_once('../Models/medicineModel.php');
    require_once('../Models/manufacturerModel.php');
    
    if(!isset($_SESSION['user_id'])){
        header('location: ../Views/login.php');
        exit();
    }
    
    if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin'){
        header('location: ../Views/verify_medicine.php');
        exit();
    }
    
    if(!isset($_REQUEST['id'])){
        header('location: ../Views/manage_medicines.php');
        exit();
    }
    
    $id = $_REQUEST['id'];
    $medicine = getMedicineById($id);
    
    if(!$medicine){
        header('location: ../Views/manage_medicines.php');
        exit();
    }
    
    // Get all manufacturers for dropdown
    $manufacturers = getAllManufacturers();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Medicine - MedVerify</title>
    <link rel="stylesheet" href="../Assets/dashboard.css">
    <script src="../Assets/validate_add_medicine.js"></script>
</head>
<body id="top">
    <header>
        <center>
            <h1>MedVerify</h1>
            <p><b>Edit Medicine</b></p>
        </center>
    </header>

    <nav>
        <center>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="manage_medicines.php">Manage Medicines</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </center>
    </nav>

    <hr>

    <main>
        <table width="100%">
            <tr>
                <td align="center">
                    <h2>✏️ Edit Medicine Information</h2>
                </td>
            </tr>
        </table>

        <br>

        <form action="../Controllers/edit_medicine.php" method="post" enctype="" onsubmit="return validateAddMedicineForm()">
        <input type="hidden" name="medicine_id" value="<?php echo $medicine['medicine_id']; ?>">
        
        <table width="100%">
            <tr>
                <td align="center">
                    <h3>📋 Basic Information</h3>
                </td>
            </tr>
        </table>

        <table border="1" width="100%">
            <tr>
                <td width="30%"><b>Medicine Name:</b> <span style="color: red;">*</span></td>
                <td width="70%"><input type="text" name="medicine_name" value="<?php echo $medicine['medicine_name']; ?>" required style="width: 100%"></td>
            </tr>
            <tr>
                <td><b>Generic Name:</b></td>
                <td><input type="text" name="generic_name" value="<?php echo $medicine['generic_name']; ?>" style="width: 100%"></td>
            </tr>
            <tr>
                <td><b>Manufacturer:</b> <span style="color: red;">*</span></td>
                <td>
                    <select name="manufacturer_id" required style="width: 100%">
                        <option value="">-- Select Manufacturer --</option>
                        <?php foreach($manufacturers as $mf){ ?>
                        <option value="<?php echo $mf['manufacturer_id']; ?>" <?php echo ($mf['manufacturer_id'] == $medicine['manufacturer_id']) ? 'selected' : ''; ?>>
                            <?php echo $mf['manufacturer_name']; ?> (<?php echo $mf['country']; ?>)
                        </option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><b>Category:</b> <span style="color: red;">*</span></td>
                <td>
                    <select name="category" required style="width: 100%">
                        <option value="Analgesic" <?php echo ($medicine['category'] == 'Analgesic') ? 'selected' : ''; ?>>Analgesic (Pain Relief)</option>
                        <option value="Antibiotic" <?php echo ($medicine['category'] == 'Antibiotic') ? 'selected' : ''; ?>>Antibiotic</option>
                        <option value="Antidiabetic" <?php echo ($medicine['category'] == 'Antidiabetic') ? 'selected' : ''; ?>>Antidiabetic</option>
                        <option value="Antihistamine" <?php echo ($medicine['category'] == 'Antihistamine') ? 'selected' : ''; ?>>Antihistamine (Allergy)</option>
                        <option value="Antacid" <?php echo ($medicine['category'] == 'Antacid') ? 'selected' : ''; ?>>Antacid</option>
                        <option value="Antitussive" <?php echo ($medicine['category'] == 'Antitussive') ? 'selected' : ''; ?>>Antitussive (Cough)</option>
                        <option value="Antiplatelet" <?php echo ($medicine['category'] == 'Antiplatelet') ? 'selected' : ''; ?>>Antiplatelet</option>
                        <option value="Vitamin" <?php echo ($medicine['category'] == 'Vitamin') ? 'selected' : ''; ?>>Vitamin/Supplement</option>
                        <option value="Cardiovascular" <?php echo ($medicine['category'] == 'Cardiovascular') ? 'selected' : ''; ?>>Cardiovascular</option>
                        <option value="Other" <?php echo ($medicine['category'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                </td>
            </tr>
        </table>

        <br>

        <table width="100%">
            <tr>
                <td align="center">
                    <h3>💊 Dosage Information</h3>
                </td>
            </tr>
        </table>

        <table border="1" width="100%">
            <tr>
                <td width="30%"><b>Dosage Form:</b> <span style="color: red;">*</span></td>
                <td width="70%">
                    <select name="dosage_form" required style="width: 100%">
                        <option value="Tablet" <?php echo ($medicine['dosage_form'] == 'Tablet') ? 'selected' : ''; ?>>Tablet</option>
                        <option value="Capsule" <?php echo ($medicine['dosage_form'] == 'Capsule') ? 'selected' : ''; ?>>Capsule</option>
                        <option value="Syrup" <?php echo ($medicine['dosage_form'] == 'Syrup') ? 'selected' : ''; ?>>Syrup</option>
                        <option value="Injection" <?php echo ($medicine['dosage_form'] == 'Injection') ? 'selected' : ''; ?>>Injection</option>
                        <option value="Ointment" <?php echo ($medicine['dosage_form'] == 'Ointment') ? 'selected' : ''; ?>>Ointment</option>
                        <option value="Drops" <?php echo ($medicine['dosage_form'] == 'Drops') ? 'selected' : ''; ?>>Drops</option>
                        <option value="Powder" <?php echo ($medicine['dosage_form'] == 'Powder') ? 'selected' : ''; ?>>Powder</option>
                        <option value="Other" <?php echo ($medicine['dosage_form'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><b>Strength:</b> <span style="color: red;">*</span></td>
                <td><input type="text" name="strength" value="<?php echo $medicine['strength']; ?>" required style="width: 100%"></td>
            </tr>
            <tr>
                <td><b>Prescription Required:</b></td>
                <td>
                    <select name="prescription_required" style="width: 100%">
                        <option value="Yes" <?php echo ($medicine['prescription_required'] == 'Yes') ? 'selected' : ''; ?>>Yes</option>
                        <option value="No" <?php echo ($medicine['prescription_required'] == 'No') ? 'selected' : ''; ?>>No</option>
                    </select>
                </td>
            </tr>
        </table>

        <br>

        <table width="100%">
            <tr>
                <td align="center">
                    <h3>🔢 Identification (Read-Only)</h3>
                </td>
            </tr>
        </table>

        <table border="1" width="100%">
            <tr>
                <td width="30%"><b>Barcode:</b></td>
                <td width="70%"><input type="text" value="<?php echo $medicine['barcode']; ?>" readonly style="width: 100%; background-color: #f0f0f0;"></td>
            </tr>
            <tr>
                <td><b>Batch Number:</b></td>
                <td><input type="text" value="<?php echo $medicine['batch_number']; ?>" readonly style="width: 100%; background-color: #f0f0f0;"></td>
            </tr>
            <tr>
                <td><b>Manufacturing Date:</b></td>
                <td><input type="date" value="<?php echo $medicine['manufacturing_date']; ?>" readonly style="width: 100%; background-color: #f0f0f0;"></td>
            </tr>
            <tr>
                <td><b>Expiry Date:</b></td>
                <td><input type="date" value="<?php echo $medicine['expiry_date']; ?>" readonly style="width: 100%; background-color: #f0f0f0;"></td>
            </tr>
        </table>

        <br>

        <table width="100%">
            <tr>
                <td align="center">
                    <h3>💰 Pricing & Additional Details</h3>
                </td>
            </tr>
        </table>

        <table border="1" width="100%">
            <tr>
                <td width="30%"><b>MRP (Maximum Retail Price):</b> <span style="color: red;">*</span></td>
                <td width="70%"><input type="number" step="0.01" name="mrp" value="<?php echo $medicine['mrp']; ?>" required style="width: 100%"></td>
            </tr>
            <tr>
                <td><b>Description:</b></td>
                <td><textarea name="description" rows="3" style="width: 100%"><?php echo $medicine['description']; ?></textarea></td>
            </tr>
            <tr>
                <td><b>Status:</b></td>
                <td>
                    <select name="status" style="width: 100%">
                        <option value="Active" <?php echo ($medicine['status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
                        <option value="Discontinued" <?php echo ($medicine['status'] == 'Discontinued') ? 'selected' : ''; ?>>Discontinued</option>
                        <option value="Recalled" <?php echo ($medicine['status'] == 'Recalled') ? 'selected' : ''; ?>>Recalled</option>
                    </select>
                </td>
            </tr>
        </table>

        <br>

        <table width="100%">
            <tr>
                <td align="center">
                    <input type="submit" name="submit" value="✅ Update Medicine" style="background-color: lightgreen; padding: 15px 30px; font-weight: bold;">
                    <a href="manage_medicines.php"><button type="button">❌ Cancel</button></a>
                </td>
            </tr>
        </table>
        </form>

        <br>

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
            <p>&copy; 2025 MedVerify | Admin Panel</p>
        </center>
    </footer>
</body>
</html>
