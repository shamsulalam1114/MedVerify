<?php include '../Controllers/manage_medicines_session.php'; ?>
<?php
    require_once('../Models/manufacturerModel.php');
    
    if(!isset($_SESSION['user_id'])){
        header('location: ../Views/login.php');
        exit();
    }
    
    if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin'){
        header('location: ../Views/verify_medicine.php');
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
    <title>Add Medicine - MedVerify</title>
    <link rel="stylesheet" href="../Assets/dashboard.css">
    <script src="../Assets/validate_add_medicine.js"></script>
</head>
<body id="top">
    <header>
        <center>
            <h1>MedVerify</h1>
            <p><b>Add New Medicine</b></p>
        </center>
    </header>

    <nav>
        <center>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="verify_medicine.php">Verify Medicine</a></li>
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
                    <h2>➕ Add New Medicine to Database</h2>
                </td>
            </tr>
        </table>

        <br>

        <form action="../Controllers/add_medicine.php" method="post" enctype="" onsubmit="return validateAddMedicineForm()">
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
                <td width="70%"><input type="text" name="medicine_name" placeholder="e.g., Paracetamol 500" required style="width: 100%"></td>
            </tr>
            <tr>
                <td><b>Generic Name:</b></td>
                <td><input type="text" name="generic_name" placeholder="e.g., Paracetamol" style="width: 100%"></td>
            </tr>
            <tr>
                <td><b>Manufacturer:</b> <span style="color: red;">*</span></td>
                <td>
                    <select name="manufacturer_id" required style="width: 100%">
                        <option value="">-- Select Manufacturer --</option>
                        <?php foreach($manufacturers as $mf){ ?>
                        <option value="<?php echo $mf['manufacturer_id']; ?>"><?php echo $mf['manufacturer_name']; ?> (<?php echo $mf['country']; ?>)</option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><b>Category:</b> <span style="color: red;">*</span></td>
                <td>
                    <select name="category" required style="width: 100%">
                        <option value="">-- Select Category --</option>
                        <option value="Analgesic">Analgesic (Pain Relief)</option>
                        <option value="Antibiotic">Antibiotic</option>
                        <option value="Antidiabetic">Antidiabetic</option>
                        <option value="Antihistamine">Antihistamine (Allergy)</option>
                        <option value="Antacid">Antacid</option>
                        <option value="Antitussive">Antitussive (Cough)</option>
                        <option value="Antiplatelet">Antiplatelet</option>
                        <option value="Vitamin">Vitamin/Supplement</option>
                        <option value="Cardiovascular">Cardiovascular</option>
                        <option value="Other">Other</option>
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
                        <option value="">-- Select Form --</option>
                        <option value="Tablet">Tablet</option>
                        <option value="Capsule">Capsule</option>
                        <option value="Syrup">Syrup</option>
                        <option value="Injection">Injection</option>
                        <option value="Ointment">Ointment</option>
                        <option value="Drops">Drops</option>
                        <option value="Powder">Powder</option>
                        <option value="Other">Other</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><b>Strength:</b> <span style="color: red;">*</span></td>
                <td><input type="text" name="strength" placeholder="e.g., 500mg, 10ml, 5%" required style="width: 100%"></td>
            </tr>
            <tr>
                <td><b>Prescription Required:</b></td>
                <td>
                    <select name="prescription_required" style="width: 100%">
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </td>
            </tr>
        </table>

        <br>

        <table width="100%">
            <tr>
                <td align="center">
                    <h3>🔢 Identification & Batch Details</h3>
                </td>
            </tr>
        </table>

        <table border="1" width="100%">
            <tr>
                <td width="30%"><b>Barcode:</b> <span style="color: red;">*</span></td>
                <td width="70%"><input type="text" name="barcode" placeholder="10-13 digit barcode" required style="width: 100%"></td>
            </tr>
            <tr>
                <td><b>Batch Number:</b> <span style="color: red;">*</span></td>
                <td><input type="text" name="batch_number" placeholder="e.g., BATCH001" required style="width: 100%"></td>
            </tr>
            <tr>
                <td><b>Manufacturing Date:</b> <span style="color: red;">*</span></td>
                <td><input type="date" name="manufacturing_date" required style="width: 100%"></td>
            </tr>
            <tr>
                <td><b>Expiry Date:</b> <span style="color: red;">*</span></td>
                <td><input type="date" name="expiry_date" required style="width: 100%"></td>
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
                <td width="70%"><input type="number" step="0.01" name="mrp" placeholder="e.g., 25.50" required style="width: 100%"></td>
            </tr>
            <tr>
                <td><b>Description:</b></td>
                <td><textarea name="description" rows="3" placeholder="Brief description of the medicine" style="width: 100%"></textarea></td>
            </tr>
            <tr>
                <td><b>Composition:</b></td>
                <td><textarea name="composition" rows="2" placeholder="Active ingredients" style="width: 100%"></textarea></td>
            </tr>
            <tr>
                <td><b>Status:</b></td>
                <td>
                    <select name="status" style="width: 100%">
                        <option value="Active">Active</option>
                        <option value="Discontinued">Discontinued</option>
                        <option value="Recalled">Recalled</option>
                    </select>
                </td>
            </tr>
        </table>

        <br>

        <table width="100%">
            <tr>
                <td align="center">
                    <input type="submit" name="submit" value="✅ Add Medicine" style="background-color: lightgreen; padding: 15px 30px; font-weight: bold;">
                    <input type="reset" value="🔄 Clear Form">
                    <a href="manage_medicines.php"><button type="button">❌ Cancel</button></a>
                </td>
            </tr>
        </table>
        </form>

        <br>

        <table width="100%">
            <tr>
                <td align="center">
                    <small><span style="color: red;">*</span> Required fields</small>
                </td>
            </tr>
        </table>

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
