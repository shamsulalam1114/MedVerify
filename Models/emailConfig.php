<?php

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com'); // Replace with your Gmail
define('SMTP_PASSWORD', 'your-app-password'); // Replace with App Password
define('SMTP_FROM_EMAIL', 'noreply@medverify.com');
define('SMTP_FROM_NAME', 'MedVerify System');

define('EMAIL_TEMPLATES_DIR', dirname(__FILE__) . '/../Views/email_templates/');

define('SYSTEM_URL', 'http://localhost/MedVerify/Views/');
define('SYSTEM_NAME', 'MedVerify - AI-Powered Medicine Authentication');

define('ENABLE_EMAIL_NOTIFICATIONS', true);
define('ADMIN_EMAIL', 'admin@medverify.com');
?>
