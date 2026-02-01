<?php
/**
 * AJAX Response Helper
 * Provides consistent JSON responses across all AJAX endpoints
 */

function sendJsonResponse($success, $message, $data = [], $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

function sendSuccessResponse($message, $data = []) {
    sendJsonResponse(true, $message, $data, 200);
}

function sendErrorResponse($message, $data = [], $statusCode = 400) {
    sendJsonResponse(false, $message, $data, $statusCode);
}

function sendValidationError($errors) {
    sendJsonResponse(false, 'Validation failed', ['errors' => $errors], 422);
}

function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}
?>
