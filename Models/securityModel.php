<?php

define('CSRF_TOKEN_EXPIRE', 3600);
define('RATE_LIMIT_REQUESTS', 100);
define('RATE_LIMIT_WINDOW', 3600);
define('SESSION_TIMEOUT', 1800);

function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time']) || 
        (time() - $_SESSION['csrf_token_time']) > CSRF_TOKEN_EXPIRE) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
        return false;
    }
    
    if ((time() - $_SESSION['csrf_token_time']) > CSRF_TOKEN_EXPIRE) {
        unset($_SESSION['csrf_token']);
        unset($_SESSION['csrf_token_time']);
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

function getCSRFInput() {
    $token = generateCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function sanitizeSQL($con, $data) {
    return mysqli_real_escape_string($con, trim($data));
}

function checkRateLimit($identifier = null) {
    if ($identifier === null) {
        $identifier = $_SERVER['REMOTE_ADDR'];
    }
    
    $rateLimitFile = sys_get_temp_dir() . '/medverify_rate_' . md5($identifier) . '.txt';
    
    $currentTime = time();
    $requests = [];
    
    if (file_exists($rateLimitFile)) {
        $content = file_get_contents($rateLimitFile);
        $requests = json_decode($content, true) ?: [];
    }
    
    $requests = array_filter($requests, function($timestamp) use ($currentTime) {
        return ($currentTime - $timestamp) < RATE_LIMIT_WINDOW;
    });
    
    if (count($requests) >= RATE_LIMIT_REQUESTS) {
        return false;
    }
    
    $requests[] = $currentTime;
    file_put_contents($rateLimitFile, json_encode($requests));
    
    return true;
}

function secureSession() {
    if (!isset($_SESSION['created'])) {
        $_SESSION['created'] = time();
    } else if (time() - $_SESSION['created'] > SESSION_TIMEOUT) {
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
    
    if (!isset($_SESSION['user_agent'])) {
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    } else if ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
        session_destroy();
        return false;
    }
    
    if (!isset($_SESSION['user_ip'])) {
        $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
    }
    
    return true;
}

function setSecurityHeaders() {
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline'; img-src 'self' data:;");
    
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function isStrongPassword($password) {
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number    = preg_match('@[0-9]@', $password);
    $length    = strlen($password) >= 8;

    return $uppercase && $lowercase && $number && $length;
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function generateRandomToken($length = 32) {
    return bin2hex(random_bytes($length));
}

function logSecurityEvent($event, $details = []) {
    $logFile = dirname(__FILE__) . '/../logs/security_log.txt';
    $logDir = dirname($logFile);
    
    if (!file_exists($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'ip' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
        'event' => $event,
        'details' => $details
    ];
    
    file_put_contents($logFile, json_encode($logEntry) . "\n", FILE_APPEND);
}

function isIPBlacklisted($ip) {
    $blacklistFile = dirname(__FILE__) . '/../config/ip_blacklist.txt';
    
    if (!file_exists($blacklistFile)) {
        return false;
    }
    
    $blacklist = file($blacklistFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return in_array($ip, $blacklist);
}

function addToBlacklist($ip, $reason = '') {
    $blacklistFile = dirname(__FILE__) . '/../config/ip_blacklist.txt';
    $logEntry = $ip . ' // ' . $reason . ' // ' . date('Y-m-d H:i:s') . "\n";
    file_put_contents($blacklistFile, $logEntry, FILE_APPEND);
}

function initSecurity() {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
        session_start();
    }
    
    setSecurityHeaders();
    
    if (isIPBlacklisted($_SERVER['REMOTE_ADDR'])) {
        http_response_code(403);
        die('Access Denied');
    }
    
    if (!checkRateLimit()) {
        http_response_code(429);
        die('Too Many Requests - Please try again later');
    }
    
    secureSession();
}

initSecurity();
?>
