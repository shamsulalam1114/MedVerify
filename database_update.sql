-- =====================================================
-- MedVerify Database Update Script
-- Medicine Authentication & Verification System
-- Date: February 1, 2026
-- =====================================================

USE medverify_new;

-- =====================================================
-- 1. MANUFACTURERS TABLE
-- Stores approved pharmaceutical manufacturers
-- =====================================================
CREATE TABLE IF NOT EXISTS manufacturers (
    manufacturer_id INT AUTO_INCREMENT PRIMARY KEY,
    manufacturer_name VARCHAR(255) NOT NULL UNIQUE,
    country VARCHAR(100) NOT NULL,
    license_number VARCHAR(100) UNIQUE,
    contact_email VARCHAR(255),
    contact_phone VARCHAR(50),
    website VARCHAR(255),
    is_verified ENUM('Yes', 'No') DEFAULT 'No',
    status ENUM('Active', 'Suspended', 'Inactive') DEFAULT 'Active',
    registered_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- 2. MEDICINES TABLE
-- Master database of registered medicines
-- =====================================================
CREATE TABLE IF NOT EXISTS medicines (
    medicine_id INT AUTO_INCREMENT PRIMARY KEY,
    medicine_name VARCHAR(255) NOT NULL,
    generic_name VARCHAR(255),
    manufacturer_id INT,
    category VARCHAR(100),
    dosage_form VARCHAR(100) COMMENT 'Tablet, Capsule, Syrup, Injection, etc.',
    strength VARCHAR(50) COMMENT 'e.g., 500mg, 10ml',
    barcode VARCHAR(100) UNIQUE,
    batch_number VARCHAR(100),
    manufacturing_date DATE,
    expiry_date DATE,
    mrp DECIMAL(10,2) COMMENT 'Maximum Retail Price',
    description TEXT,
    composition TEXT COMMENT 'Active ingredients',
    side_effects TEXT,
    storage_conditions VARCHAR(255),
    prescription_required ENUM('Yes', 'No') DEFAULT 'Yes',
    status ENUM('Active', 'Discontinued', 'Recalled') DEFAULT 'Active',
    image_path VARCHAR(500),
    registered_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (manufacturer_id) REFERENCES manufacturers(manufacturer_id) ON DELETE SET NULL,
    INDEX idx_medicine_name (medicine_name),
    INDEX idx_barcode (barcode),
    INDEX idx_batch (batch_number),
    INDEX idx_manufacturer (manufacturer_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- 3. MEDICINE VERIFICATIONS TABLE
-- Track all medicine verification attempts
-- =====================================================
CREATE TABLE IF NOT EXISTS medicine_verifications (
    verification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    medicine_id INT NULL COMMENT 'NULL if medicine not found in database',
    barcode_scanned VARCHAR(100),
    batch_number_entered VARCHAR(100),
    verification_method ENUM('Barcode', 'Manual', 'QR Code', 'Image Recognition') DEFAULT 'Manual',
    verification_result ENUM('Genuine', 'Counterfeit', 'Suspicious', 'Not Found', 'Expired') NOT NULL,
    confidence_score DECIMAL(5,2) COMMENT 'AI confidence percentage (0-100)',
    expiry_check ENUM('Valid', 'Expired', 'Near Expiry', 'Not Available') DEFAULT 'Not Available',
    manufacturer_match ENUM('Match', 'Mismatch', 'Not Available') DEFAULT 'Not Available',
    batch_match ENUM('Match', 'Mismatch', 'Not Available') DEFAULT 'Not Available',
    image_uploaded VARCHAR(500) COMMENT 'User uploaded medicine image',
    verification_notes TEXT COMMENT 'Additional details or warnings',
    ip_address VARCHAR(50),
    location VARCHAR(255),
    verified_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (medicine_id) REFERENCES medicines(medicine_id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_medicine (medicine_id),
    INDEX idx_result (verification_result),
    INDEX idx_verified_at (verified_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- 4. VERIFICATION ALERTS TABLE
-- Store suspicious activities and alerts
-- =====================================================
CREATE TABLE IF NOT EXISTS verification_alerts (
    alert_id INT AUTO_INCREMENT PRIMARY KEY,
    verification_id INT,
    alert_type ENUM('Counterfeit Detected', 'Expired Medicine', 'Recalled Medicine', 'Multiple Failed Attempts', 'Suspicious Pattern') NOT NULL,
    severity ENUM('Low', 'Medium', 'High', 'Critical') DEFAULT 'Medium',
    alert_message TEXT,
    is_resolved ENUM('Yes', 'No') DEFAULT 'No',
    resolved_by INT NULL COMMENT 'Admin user_id who resolved',
    resolved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (verification_id) REFERENCES medicine_verifications(verification_id) ON DELETE CASCADE,
    FOREIGN KEY (resolved_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_verification (verification_id),
    INDEX idx_severity (severity),
    INDEX idx_resolved (is_resolved)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- 5. UPDATE EXISTING VERIFICATIONS TABLE
-- Add medicine_verification_id reference
-- =====================================================
ALTER TABLE verifications 
ADD COLUMN medicine_verification_id INT NULL AFTER verification_type,
ADD FOREIGN KEY (medicine_verification_id) REFERENCES medicine_verifications(verification_id) ON DELETE SET NULL;

-- =====================================================
-- 6. MEDICINE BATCHES TABLE
-- Track different batches of same medicine
-- =====================================================
CREATE TABLE IF NOT EXISTS medicine_batches (
    batch_id INT AUTO_INCREMENT PRIMARY KEY,
    medicine_id INT NOT NULL,
    batch_number VARCHAR(100) NOT NULL,
    manufacturing_date DATE,
    expiry_date DATE NOT NULL,
    quantity_manufactured INT,
    warehouse_location VARCHAR(255),
    qr_code VARCHAR(255) UNIQUE,
    status ENUM('In Stock', 'Distributed', 'Recalled', 'Expired') DEFAULT 'In Stock',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (medicine_id) REFERENCES medicines(medicine_id) ON DELETE CASCADE,
    UNIQUE KEY unique_batch (medicine_id, batch_number),
    INDEX idx_batch_number (batch_number),
    INDEX idx_expiry (expiry_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- 7. REPORTED COUNTERFEITS TABLE
-- User-reported fake medicines
-- =====================================================
CREATE TABLE IF NOT EXISTS reported_counterfeits (
    report_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    medicine_name VARCHAR(255),
    suspected_manufacturer VARCHAR(255),
    barcode VARCHAR(100),
    batch_number VARCHAR(100),
    purchase_location VARCHAR(500),
    purchase_date DATE,
    report_description TEXT,
    evidence_image VARCHAR(500),
    verification_status ENUM('Pending', 'Verified Fake', 'Genuine', 'Under Investigation') DEFAULT 'Pending',
    admin_notes TEXT,
    reviewed_by INT NULL,
    reviewed_at TIMESTAMP NULL,
    reported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_status (verification_status),
    INDEX idx_reported_at (reported_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- SAMPLE DATA FOR TESTING
-- =====================================================

-- Insert Sample Manufacturers
INSERT INTO manufacturers (manufacturer_name, country, license_number, contact_email, is_verified, status) VALUES
('Pfizer Inc.', 'USA', 'MFG-USA-001', 'contact@pfizer.com', 'Yes', 'Active'),
('GlaxoSmithKline', 'UK', 'MFG-UK-002', 'info@gsk.com', 'Yes', 'Active'),
('Sun Pharmaceutical', 'India', 'MFG-IND-003', 'support@sunpharma.com', 'Yes', 'Active'),
('Cipla Ltd', 'India', 'MFG-IND-004', 'info@cipla.com', 'Yes', 'Active'),
('Novartis', 'Switzerland', 'MFG-SWI-005', 'contact@novartis.com', 'Yes', 'Active');

-- Insert Sample Medicines
INSERT INTO medicines (medicine_name, generic_name, manufacturer_id, category, dosage_form, strength, barcode, batch_number, manufacturing_date, expiry_date, mrp, description, prescription_required, status) VALUES
('Paracetamol 500', 'Paracetamol', 1, 'Analgesic', 'Tablet', '500mg', '8901234567890', 'BATCH001', '2025-01-15', '2027-01-15', 25.50, 'Pain relief and fever reducer', 'No', 'Active'),
('Amoxicillin Capsules', 'Amoxicillin', 2, 'Antibiotic', 'Capsule', '250mg', '8901234567891', 'BATCH002', '2025-02-01', '2027-02-01', 120.00, 'Antibiotic for bacterial infections', 'Yes', 'Active'),
('Cetirizine 10mg', 'Cetirizine', 3, 'Antihistamine', 'Tablet', '10mg', '8901234567892', 'BATCH003', '2025-03-10', '2027-03-10', 35.00, 'Relief from allergies', 'No', 'Active'),
('Metformin 500', 'Metformin', 4, 'Antidiabetic', 'Tablet', '500mg', '8901234567893', 'BATCH004', '2024-12-20', '2026-12-20', 45.00, 'Type 2 diabetes management', 'Yes', 'Active'),
('Ibuprofen 400', 'Ibuprofen', 5, 'Analgesic', 'Tablet', '400mg', '8901234567894', 'BATCH005', '2025-01-25', '2027-01-25', 30.00, 'Pain and inflammation relief', 'No', 'Active'),
('Azithromycin 500', 'Azithromycin', 1, 'Antibiotic', 'Tablet', '500mg', '8901234567895', 'BATCH006', '2025-02-15', '2027-02-15', 180.00, 'Broad spectrum antibiotic', 'Yes', 'Active'),
('Omeprazole 20mg', 'Omeprazole', 2, 'Antacid', 'Capsule', '20mg', '8901234567896', 'BATCH007', '2025-01-10', '2027-01-10', 65.00, 'Reduces stomach acid', 'Yes', 'Active'),
('Aspirin 75mg', 'Acetylsalicylic Acid', 3, 'Antiplatelet', 'Tablet', '75mg', '8901234567897', 'BATCH008', '2024-11-05', '2026-11-05', 15.00, 'Blood thinner', 'No', 'Active'),
('Vitamin C 500', 'Ascorbic Acid', 4, 'Vitamin', 'Tablet', '500mg', '8901234567898', 'BATCH009', '2025-03-01', '2027-03-01', 50.00, 'Immune system support', 'No', 'Active'),
('Cough Syrup', 'Dextromethorphan', 5, 'Antitussive', 'Syrup', '100ml', '8901234567899', 'BATCH010', '2025-02-20', '2027-02-20', 85.00, 'Cough suppressant', 'No', 'Active');

-- Insert Sample Medicine Batches
INSERT INTO medicine_batches (medicine_id, batch_number, manufacturing_date, expiry_date, quantity_manufactured, status) VALUES
(1, 'BATCH001', '2025-01-15', '2027-01-15', 10000, 'Distributed'),
(2, 'BATCH002', '2025-02-01', '2027-02-01', 5000, 'In Stock'),
(3, 'BATCH003', '2025-03-10', '2027-03-10', 8000, 'In Stock'),
(4, 'BATCH004', '2024-12-20', '2026-12-20', 12000, 'Distributed'),
(5, 'BATCH005', '2025-01-25', '2027-01-25', 15000, 'In Stock');

-- Insert Sample Verification (for testing)
-- Note: You'll need existing user_id from users table
-- INSERT INTO medicine_verifications (user_id, medicine_id, barcode_scanned, verification_method, verification_result, confidence_score, expiry_check) VALUES
-- (1, 1, '8901234567890', 'Barcode', 'Genuine', 98.50, 'Valid');

-- =====================================================
-- CREATE VIEWS FOR REPORTING
-- =====================================================

-- View: Recent Verifications with Details
CREATE OR REPLACE VIEW v_recent_verifications AS
SELECT 
    mv.verification_id,
    u.username,
    u.full_name,
    m.medicine_name,
    mf.manufacturer_name,
    mv.barcode_scanned,
    mv.verification_method,
    mv.verification_result,
    mv.confidence_score,
    mv.expiry_check,
    mv.verified_at
FROM medicine_verifications mv
LEFT JOIN users u ON mv.user_id = u.user_id
LEFT JOIN medicines m ON mv.medicine_id = m.medicine_id
LEFT JOIN manufacturers mf ON m.manufacturer_id = mf.manufacturer_id
ORDER BY mv.verified_at DESC;

-- View: Counterfeit Statistics
CREATE OR REPLACE VIEW v_counterfeit_stats AS
SELECT 
    verification_result,
    COUNT(*) as count,
    DATE(verified_at) as verification_date
FROM medicine_verifications
GROUP BY verification_result, DATE(verified_at)
ORDER BY verification_date DESC;

-- View: Expiring Medicines
CREATE OR REPLACE VIEW v_expiring_medicines AS
SELECT 
    m.medicine_id,
    m.medicine_name,
    mf.manufacturer_name,
    mb.batch_number,
    mb.expiry_date,
    DATEDIFF(mb.expiry_date, CURDATE()) as days_to_expiry
FROM medicines m
JOIN manufacturers mf ON m.manufacturer_id = mf.manufacturer_id
JOIN medicine_batches mb ON m.medicine_id = mb.medicine_id
WHERE mb.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 90 DAY)
AND mb.status = 'In Stock'
ORDER BY mb.expiry_date ASC;

-- =====================================================
-- CREATE INDEXES FOR PERFORMANCE
-- =====================================================
CREATE INDEX idx_verification_date ON medicine_verifications(verified_at);
CREATE INDEX idx_medicine_status ON medicines(status);
CREATE INDEX idx_manufacturer_status ON manufacturers(status);
CREATE INDEX idx_batch_status ON medicine_batches(status);

-- =====================================================
-- END OF DATABASE UPDATE SCRIPT
-- =====================================================

-- To verify the tables were created successfully, run:
-- SHOW TABLES;
-- DESCRIBE medicines;
-- DESCRIBE manufacturers;
-- DESCRIBE medicine_verifications;
