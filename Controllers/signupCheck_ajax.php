<?php
session_start();
require_once('../Models/userModel.php');
require_once('ajax_response.php');

if(isset($_POST['submit']) || isAjaxRequest()){
    $full_name = trim($_REQUEST['full_name'] ?? '');
    $username = trim($_REQUEST['username'] ?? '');
    $password = $_REQUEST['password'] ?? '';
    $confirm_password = $_REQUEST['confirm_password'] ?? '';
    
    $errors = [];
    
    // Full name validation
    if($full_name == ""){
        $errors['full_name'] = "Full name is required!";
    } elseif(strlen($full_name) < 3){
        $errors['full_name'] = "Full name must be at least 3 characters!";
    } elseif(strlen($full_name) > 50){
        $errors['full_name'] = "Full name must be less than 50 characters!";
    } else {
        $hasInvalidChar = false;
        for($i = 0; $i < strlen($full_name); $i++){
            $char = $full_name[$i];
            if(!(($char >= 'a' && $char <= 'z') || ($char >= 'A' && $char <= 'Z') || $char == ' ')){
                $hasInvalidChar = true;
                break;
            }
        }
        if($hasInvalidChar){
            $errors['full_name'] = "Full name should only contain letters and spaces!";
        }
    }
    
    // Username validation
    if($username == ""){
        $errors['username'] = "Username is required!";
    } elseif(strlen($username) < 4){
        $errors['username'] = "Username must be at least 4 characters!";
    } elseif(strlen($username) > 20){
        $errors['username'] = "Username must be less than 20 characters!";
    } else {
        // Check if username already exists
        if(checkUsernameExists($username)){
            $errors['username'] = "Username already exists! Please choose a different one.";
        }
    }
    
    // Password validation
    if($password == ""){
        $errors['password'] = "Password is required!";
    } elseif(strlen($password) < 6){
        $errors['password'] = "Password must be at least 6 characters!";
    } elseif(strlen($password) > 30){
        $errors['password'] = "Password must be less than 30 characters!";
    }
    
    // Confirm password validation
    if($confirm_password == ""){
        $errors['confirm_password'] = "Please confirm your password!";
    } elseif($password != $confirm_password){
        $errors['confirm_password'] = "Passwords do not match!";
    }
    
    if(!empty($errors)){
        if(isAjaxRequest()){
            sendValidationError($errors);
        }
        $_SESSION['error'] = implode(', ', $errors);
        header('location: ../Views/signup.php');
        exit();
    }
    
    // Create user
    $user = [
        'full_name' => $full_name,
        'username' => $username,
        'password' => $password,
        'user_type' => 'patient'
    ];
    
    $result = addUser($user);
    
    if($result){
        if(isAjaxRequest()){
            sendSuccessResponse("Account created successfully! Redirecting to login...", [
                'user_id' => $result
            ]);
        }
        $_SESSION['success'] = "Account created successfully! Please login.";
        header('location: ../Views/login.php');
    }else{
        if(isAjaxRequest()){
            sendErrorResponse("Failed to create account. Please try again.");
        }
        $_SESSION['error'] = "Failed to create account. Please try again.";
        header('location: ../Views/signup.php');
    }
} else {
    header('location: ../Views/signup.php');
}
?>
