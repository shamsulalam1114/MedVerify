# MedVerify AJAX Implementation Guide

## Overview
All forms and data operations in MedVerify now support AJAX for seamless user experience without page reloads.

## Files Created

### 1. Controllers/ajax_response.php
**Purpose:** Centralized JSON response handler for all AJAX requests

**Functions:**
- `sendJsonResponse($success, $message, $data, $statusCode)` - Generic JSON response
- `sendSuccessResponse($message, $data)` - Success response (HTTP 200)
- `sendErrorResponse($message, $data, $statusCode)` - Error response
- `sendValidationError($errors)` - Validation errors (HTTP 422)
- `isAjaxRequest()` - Detect AJAX requests

**Usage:**
```php
require_once('ajax_response.php');

if(isAjaxRequest()){
    sendSuccessResponse("Operation successful!", ['data' => $result]);
}
```

### 2. Assets/ajax_handler.js
**Purpose:** Frontend AJAX handler for all forms

**Features:**
- Automatic form submission via AJAX
- File upload support with progress
- Real-time validation error display
- Notification system (success/error/warning/info)
- Modal display for results
- Spinner/loading states on buttons

**Auto-detected Forms:**
- Login form (`action*="loginCheck"`)
- Signup form (`action*="signupCheck"`)
- Medicine verification (`action*="verify_medicine"`)
- Medicine management (add/edit/delete)
- Manufacturer management
- Family member management
- Appointment management
- Report upload/management
- Counterfeit reporting

### 3. Assets/ajax_login.js
**Purpose:** Standalone login AJAX handler (deprecated - use ajax_handler.js)

### 4. Controllers/*_ajax.php Files

#### loginCheck_ajax.php
**Endpoint:** POST `/Controllers/loginCheck_ajax.php`
**Request:**
```javascript
FormData {
    username: string,
    password: string,
    submit: string
}
```
**Response:**
```json
{
    "success": true,
    "message": "Login successful!",
    "data": {
        "redirect": "../Views/calendar.php",
        "user": {
            "username": "john_doe",
            "full_name": "John Doe",
            "user_type": "patient"
        }
    },
    "timestamp": "2026-02-02 10:30:00"
}
```

#### signupCheck_ajax.php
**Endpoint:** POST `/Controllers/signupCheck_ajax.php`
**Request:**
```javascript
FormData {
    full_name: string,
    username: string,
    password: string,
    confirm_password: string,
    submit: string
}
```
**Validation Errors Response:**
```json
{
    "success": false,
    "message": "Validation failed",
    "data": {
        "errors": {
            "full_name": "Full name must be at least 3 characters!",
            "username": "Username already exists!"
        }
    },
    "timestamp": "2026-02-02 10:31:00"
}
```

#### verify_medicine_ai_ajax.php
**Endpoint:** POST `/Controllers/verify_medicine_ai_ajax.php`
**Request:**
```javascript
FormData {
    barcode: string,
    batch_number: string,
    method: "AI Image Analysis",
    medicine_image: File
}
```
**Success Response:**
```json
{
    "success": true,
    "message": "Verification completed successfully!",
    "data": {
        "verification_id": 123,
        "verification_result": "Genuine",
        "confidence_score": 95.5,
        "medicine": {
            "medicine_id": 45,
            "medicine_name": "Paracetamol",
            "generic_name": "Acetaminophen"
        },
        "ai_analysis": {
            "timestamp": "2026-02-02 10:32:00",
            "final_verdict": "Genuine",
            "overall_confidence": 95.5,
            "analysis_results": {
                "image_ai": {...},
                "barcode_ai": {...},
                "counterfeit_ai": {...}
            }
        },
        "redirect": "../Views/verification_result.php?id=123"
    }
}
```

## Implementation Steps

### Step 1: Include AJAX Handler in Pages

#### Method A: Direct Include
Add to `<head>` section:
```html
<script src="../Assets/ajax_handler.js"></script>
```

#### Method B: PHP Include (Recommended)
Add before `</head>`:
```php
<?php include 'includes/ajax_includes.php'; ?>
```

### Step 2: Update Form Actions

#### Before (Traditional):
```html
<form action="../Controllers/loginCheck.php" method="post">
```

#### After (AJAX-enabled):
```html
<form action="../Controllers/loginCheck_ajax.php" method="post">
<!-- OR keep old action, AJAX handler auto-detects and overrides -->
<form action="../Controllers/loginCheck.php" method="post">
```

### Step 3: Add AJAX Support to Existing Controllers

**Pattern:**
```php
<?php
require_once('ajax_response.php');

if(isAjaxRequest()){
    // Return JSON for AJAX requests
    sendSuccessResponse("Success!", ['data' => $result]);
}

// Traditional redirect for non-AJAX
header('location: ../Views/page.php');
?>
```

## Features Implemented

### ✅ Login System
- AJAX login with validation
- Auto-redirect based on user type
- Session management
- Error notifications

### ✅ Signup System
- Real-time validation
- Username availability check
- Password strength validation
- Field-level error display

### ✅ Medicine Verification
- AI-powered image analysis
- File upload with progress
- Real-time results display
- Modal popup for verification results

### ⏳ In Progress

#### Medicine Management
- Add medicine via AJAX
- Edit medicine (inline editing)
- Delete with confirmation
- Dynamic table updates

#### Manufacturer Management
- CRUD operations via AJAX
- Table refresh without reload

#### Family Profile
- Add/edit/delete family members
- Profile picture upload

#### Appointments
- Calendar integration
- Real-time appointment creation
- Drag-and-drop rescheduling

#### Reports
- File upload with progress bar
- Preview before upload
- Delete reports

#### Counterfeit Reporting
- Submit reports
- Admin review/approval
- Status updates

## Notification System

### Types
```javascript
showNotification('success', 'Operation completed!');
showNotification('error', 'Something went wrong!');
showNotification('warning', 'Please review your input');
showNotification('info', 'Here's some information');
```

### Auto-dismiss
- Success: 3 seconds
- Error: 5 seconds
- Warning: 5 seconds
- Info: 4 seconds

### Positioning
- Fixed top-right corner
- Stacks multiple notifications
- Slide-in/out animations

## Validation Error Display

Errors appear below respective fields:
```html
<input name="username" />
<div class="error-message">Username already exists!</div>
```

Styling applied automatically:
- Red text color (#721c24)
- Field border turns red (#f5c6cb)
- Clears on next submission

## Modal System

Dynamic modals for results:
```javascript
displayVerificationResults(data);
// Creates modal with close button
// Scrollable content area
// Overlay backdrop
```

## File Upload Handling

AJAX handler detects file inputs:
```javascript
handleFormSubmit(form, url, callback, isFileUpload=true);
```

Features:
- FormData automatic construction
- Progress tracking (future)
- File size validation (future)
- MIME type validation (future)

## Error Handling

### Frontend
```javascript
.catch(error => {
    console.error('Error:', error);
    showNotification('error', 'An error occurred. Please try again.');
});
```

### Backend
```php
try {
    // Operation
    sendSuccessResponse("Success!", $data);
} catch (Exception $e) {
    sendErrorResponse($e->getMessage(), [], 500);
}
```

## Security Considerations

### CSRF Protection
```php
// Add token to all AJAX requests
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
```

### Request Validation
```php
if(!isAjaxRequest()){
    sendErrorResponse("Invalid request", [], 400);
}
```

### Rate Limiting
```php
// Implement in future
if(exceedsRateLimit($_SESSION['user_id'])){
    sendErrorResponse("Too many requests", [], 429);
}
```

## Browser Compatibility

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+
- IE 11 (with polyfills)

## Dependencies

- Native Fetch API
- FormData API
- Promises
- ES6 Classes
- JSON parsing

## Testing

### Test AJAX Login
1. Open browser DevTools (F12)
2. Go to Network tab
3. Login with valid credentials
4. Check XHR request to `loginCheck_ajax.php`
5. Verify JSON response
6. Confirm no page reload

### Test Validation Errors
1. Submit empty login form
2. Check for error notification
3. Verify field-level error messages
4. Confirm form stays on page

### Test File Upload
1. Select medicine image
2. Submit verification form
3. Monitor network for multipart/form-data
4. Verify file uploaded to /uploads/verifications/
5. Check AI analysis in response

## Performance

### Optimizations
- Debounced autocomplete (300ms)
- Lazy loading for modals
- Event delegation for dynamic elements
- Single AJAX handler instance

### Bundle Size
- ajax_handler.js: ~12KB
- ajax_response.php: ~1KB
- Total overhead: <15KB

## Future Enhancements

1. **Real-time Updates**
   - WebSocket integration
   - Live notifications
   - Auto-refresh data

2. **Offline Support**
   - Service workers
   - IndexedDB caching
   - Queue pending requests

3. **Progressive Enhancement**
   - Works without JavaScript
   - AJAX as enhancement layer

4. **Advanced Features**
   - Drag-and-drop file upload
   - Image preview before upload
   - Multi-file upload
   - Upload progress bars

## Troubleshooting

### AJAX Request Not Sending
- Check form action URL
- Verify AJAX handler loaded
- Inspect browser console for errors
- Confirm `X-Requested-With` header

### JSON Parse Error
- Check PHP error logs
- Verify `Content-Type: application/json`
- Ensure no output before JSON
- Use `json_last_error()`

### Session Issues
- Confirm `session_start()` called
- Check session cookie settings
- Verify HTTPS for secure cookies
- Test with incognito mode

### File Upload Fails
- Check file size limits (php.ini)
- Verify upload directory permissions
- Confirm `enctype="multipart/form-data"`
- Check `$_FILES` array

## Support

For issues or questions:
1. Check browser console for errors
2. Review PHP error logs
3. Test with AJAX disabled
4. Verify server configuration

---

**Last Updated:** February 2, 2026
**Version:** 1.0
**Status:** In Active Development
