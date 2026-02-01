<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once '../Models/manufacturerModel.php';

// Get manufacturer ID
$manufacturerId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($manufacturerId === 0) {
    header("Location: manage_manufacturers.php?error=Invalid manufacturer ID");
    exit();
}

// Get manufacturer details
$manufacturer = getManufacturerById($manufacturerId);

if (!$manufacturer) {
    header("Location: manage_manufacturers.php?error=Manufacturer not found");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Manufacturer - MedVerify</title>
    <link rel="stylesheet" href="../Assets/dashboard.css">
</head>
<body>
    <nav>
        <div class="nav-brand">MedVerify - AI-Powered Medicine Authentication</div>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="verify_medicine.php">Verify Medicine</a></li>
            <li><a href="verification_history.php">Verification History</a></li>
            <li><a href="manage_medicines.php">Manage Medicines</a></li>
            <li><a href="manage_manufacturers.php" class="active">Manage Manufacturers</a></li>
            <li><a href="review_counterfeits.php">Review Reports</a></li>
            <li><a href="report_counterfeit.php">Report Counterfeit</a></li>
            <li><a href="family_profile.php">Family Profile</a></li>
            <li><a href="calendar.php">Calendar</a></li>
            <li><a href="upload_report.php">Upload Report</a></li>
            <li><a href="view_reports.php">View Reports</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="header">
            <h1>Edit Manufacturer</h1>
            <button onclick="window.location.href='manage_manufacturers.php'" class="btn-secondary">← Back to List</button>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <form id="editManufacturerForm" method="POST" action="../Controllers/edit_manufacturer.php" onsubmit="return validateEditManufacturer()">
                <input type="hidden" name="manufacturer_id" value="<?php echo $manufacturer['manufacturer_id']; ?>">
                
                <div class="form-section">
                    <h3>Basic Information</h3>
                    
                    <div class="form-group">
                        <label for="name">Manufacturer Name *</label>
                        <input type="text" id="name" name="name" required maxlength="255" value="<?php echo htmlspecialchars($manufacturer['name']); ?>">
                        <span id="nameError" class="error-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="country">Country *</label>
                        <select id="country" name="country" required>
                            <option value="">Select Country</option>
                            <option value="India" <?php echo $manufacturer['country'] === 'India' ? 'selected' : ''; ?>>India</option>
                            <option value="USA" <?php echo $manufacturer['country'] === 'USA' ? 'selected' : ''; ?>>USA</option>
                            <option value="UK" <?php echo $manufacturer['country'] === 'UK' ? 'selected' : ''; ?>>UK</option>
                            <option value="Germany" <?php echo $manufacturer['country'] === 'Germany' ? 'selected' : ''; ?>>Germany</option>
                            <option value="France" <?php echo $manufacturer['country'] === 'France' ? 'selected' : ''; ?>>France</option>
                            <option value="China" <?php echo $manufacturer['country'] === 'China' ? 'selected' : ''; ?>>China</option>
                            <option value="Japan" <?php echo $manufacturer['country'] === 'Japan' ? 'selected' : ''; ?>>Japan</option>
                            <option value="Switzerland" <?php echo $manufacturer['country'] === 'Switzerland' ? 'selected' : ''; ?>>Switzerland</option>
                            <option value="Canada" <?php echo $manufacturer['country'] === 'Canada' ? 'selected' : ''; ?>>Canada</option>
                            <option value="Australia" <?php echo $manufacturer['country'] === 'Australia' ? 'selected' : ''; ?>>Australia</option>
                            <option value="Other" <?php echo $manufacturer['country'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                        <span id="countryError" class="error-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" rows="3" maxlength="500"><?php echo htmlspecialchars($manufacturer['address'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="website">Website</label>
                        <input type="url" id="website" name="website" maxlength="255" value="<?php echo htmlspecialchars($manufacturer['website'] ?? ''); ?>">
                        <span id="websiteError" class="error-message"></span>
                    </div>
                </div>

                <div class="form-section">
                    <h3>License Information</h3>
                    
                    <div class="form-group">
                        <label for="license_number">License Number *</label>
                        <input type="text" id="license_number" name="license_number" required maxlength="100" value="<?php echo htmlspecialchars($manufacturer['license_number']); ?>">
                        <span id="licenseNumberError" class="error-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="license_expiry">License Expiry Date</label>
                        <input type="date" id="license_expiry" name="license_expiry" value="<?php echo $manufacturer['license_expiry']; ?>">
                        <span id="licenseExpiryError" class="error-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="certifications">Certifications</label>
                        <textarea id="certifications" name="certifications" rows="3" maxlength="500"><?php echo htmlspecialchars($manufacturer['certifications'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Contact Information</h3>
                    
                    <div class="form-group">
                        <label for="contact_email">Contact Email</label>
                        <input type="email" id="contact_email" name="contact_email" maxlength="100" value="<?php echo htmlspecialchars($manufacturer['contact_email'] ?? ''); ?>">
                        <span id="contactEmailError" class="error-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="contact_phone">Contact Phone</label>
                        <input type="tel" id="contact_phone" name="contact_phone" maxlength="20" value="<?php echo htmlspecialchars($manufacturer['contact_phone'] ?? ''); ?>">
                        <span id="contactPhoneError" class="error-message"></span>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Status</h3>
                    
                    <div class="form-group">
                        <label for="status">Manufacturer Status *</label>
                        <select id="status" name="status" required>
                            <option value="Active" <?php echo $manufacturer['status'] === 'Active' ? 'selected' : ''; ?>>Active</option>
                            <option value="Inactive" <?php echo $manufacturer['status'] === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Update Manufacturer</button>
                    <button type="button" onclick="window.location.href='manage_manufacturers.php'" class="btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../Assets/validate_edit_manufacturer.js"></script>
</body>
</html>
