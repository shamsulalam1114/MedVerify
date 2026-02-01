<?php
require_once('emailConfig.php');

function sendEmail($to, $subject, $htmlBody, $plainTextBody = ''){
    if (!ENABLE_EMAIL_NOTIFICATIONS) {
        return true;
    }
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">\r\n";
    $headers .= "Reply-To: " . SMTP_FROM_EMAIL . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    return mail($to, $subject, $htmlBody, $headers);
}

function sendWelcomeEmail($userEmail, $userName){
    $subject = "Welcome to " . SYSTEM_NAME;
    
    $htmlBody = getEmailTemplate('welcome', [
        'user_name' => $userName,
        'login_url' => SYSTEM_URL . 'login.php',
        'dashboard_url' => SYSTEM_URL . 'dashboard.php'
    ]);
    
    return sendEmail($userEmail, $subject, $htmlBody);
}

function sendCounterfeitStatusEmail($userEmail, $userName, $reportId, $status, $adminNotes = ''){
    $subject = "Counterfeit Report #$reportId - Status Update";
    
    $statusMessage = '';
    if ($status === 'Verified') {
        $statusMessage = 'Your report has been verified and confirmed as genuine. Thank you for helping keep our community safe!';
    } elseif ($status === 'Rejected') {
        $statusMessage = 'After investigation, your report could not be confirmed. ' . ($adminNotes ? 'Reason: ' . $adminNotes : '');
    }
    
    $htmlBody = getEmailTemplate('counterfeit_status', [
        'user_name' => $userName,
        'report_id' => $reportId,
        'status' => $status,
        'status_message' => $statusMessage,
        'admin_notes' => $adminNotes,
        'view_url' => SYSTEM_URL . 'report_counterfeit.php'
    ]);
    
    return sendEmail($userEmail, $subject, $htmlBody);
}

function sendExpiryAlertEmail($userEmail, $userName, $medicines){
    $subject = "⚠️ Medicine Expiry Alert - MedVerify";
    
    $medicineList = '';
    foreach ($medicines as $medicine) {
        $medicineList .= "<li><strong>{$medicine['medicine_name']}</strong> - Expires: {$medicine['expiry_date']}</li>";
    }
    
    $htmlBody = getEmailTemplate('expiry_alert', [
        'user_name' => $userName,
        'medicine_count' => count($medicines),
        'medicine_list' => $medicineList,
        'dashboard_url' => SYSTEM_URL . 'dashboard.php'
    ]);
    
    return sendEmail($userEmail, $subject, $htmlBody);
}

function sendVerificationSummaryEmail($userEmail, $userName, $stats){
    $subject = "Your Verification Summary - MedVerify";
    
    $htmlBody = getEmailTemplate('verification_summary', [
        'user_name' => $userName,
        'total_verifications' => $stats['total'],
        'genuine_count' => $stats['genuine'],
        'suspicious_count' => $stats['suspicious'],
        'counterfeit_count' => $stats['counterfeit'],
        'period' => $stats['period'],
        'dashboard_url' => SYSTEM_URL . 'dashboard.php'
    ]);
    
    return sendEmail($userEmail, $subject, $htmlBody);
}

function sendAdminCounterfeitAlert($reportId, $medicineName, $userName){
    $subject = "🚨 New Counterfeit Report #$reportId";
    
    $htmlBody = getEmailTemplate('admin_counterfeit_alert', [
        'report_id' => $reportId,
        'medicine_name' => $medicineName,
        'user_name' => $userName,
        'review_url' => SYSTEM_URL . 'review_counterfeits.php'
    ]);
    
    return sendEmail(ADMIN_EMAIL, $subject, $htmlBody);
}

function sendPasswordResetEmail($userEmail, $userName, $resetToken){
    $subject = "Password Reset Request - MedVerify";
    
    $resetUrl = SYSTEM_URL . "reset_password.php?token=$resetToken";
    
    $htmlBody = getEmailTemplate('password_reset', [
        'user_name' => $userName,
        'reset_url' => $resetUrl,
        'expiry_time' => '24 hours'
    ]);
    
    return sendEmail($userEmail, $subject, $htmlBody);
}

function getEmailTemplate($templateName, $variables = []){
    $templatePath = EMAIL_TEMPLATES_DIR . $templateName . '.html';
    
    if (!file_exists($templatePath)) {
        return getGenericEmailTemplate($variables);
    }
    
    $template = file_get_contents($templatePath);
    
    foreach ($variables as $key => $value) {
        $template = str_replace('{{' . $key . '}}', $value, $template);
    }
    
    return $template;
}

function getGenericEmailTemplate($variables){
    return "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
        .content { background: #f9f9f9; padding: 30px; }
        .button { display: inline-block; padding: 12px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 10px 0; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>" . SYSTEM_NAME . "</h1>
        </div>
        <div class='content'>
            {$variables['content']}
        </div>
        <div class='footer'>
            <p>This is an automated message from MedVerify System.</p>
            <p>&copy; 2026 MedVerify. All rights reserved.</p>
        </div>
    </div>
</body>
</html>";
}

function logEmail($to, $subject, $status){
    $logFile = dirname(__FILE__) . '/../logs/email_log.txt';
    $logDir = dirname($logFile);
    
    if (!file_exists($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $logEntry = date('Y-m-d H:i:s') . " | TO: $to | SUBJECT: $subject | STATUS: " . ($status ? 'SUCCESS' : 'FAILED') . "\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}
?>
