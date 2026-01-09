<?php
    session_start();
    require_once('../Models/userModel.php');
    
    if(isset($_POST['submit'])){
        $full_name = trim($_REQUEST['full_name']);
        $username = trim($_REQUEST['username']);
        $password = $_REQUEST['password'];
        $confirm_password = $_REQUEST['confirm_password'];
        
        // Full name validation
        if($full_name == ""){
            $_SESSION['error'] = "Full name is required!";
            header('location: ../Views/signup.php');
            exit();
        }
        
        if(strlen($full_name) < 3){
            $_SESSION['error'] = "Full name must be at least 3 characters!";
            header('location: ../Views/signup.php');
            exit();
        }
        
        if(strlen($full_name) > 50){
            $_SESSION['error'] = "Full name must be less than 50 characters!";
            header('location: ../Views/signup.php');
            exit();
        }
        
        
        $hasInvalidChar = false;
        for($i = 0; $i < strlen($full_name); $i++){
            $char = $full_name[$i];
            if(!(($char >= 'a' && $char <= 'z') || ($char >= 'A' && $char <= 'Z') || $char == ' ')){
                $hasInvalidChar = true;
                break;
            }
        }
        
        if($hasInvalidChar){
            $_SESSION['error'] = "Full name should only contain letters and spaces!";
            header('location: ../Views/signup.php');
            exit();
        }
        
        
        if($username == ""){
            $_SESSION['error'] = "Username is required!";
            header('location: ../Views/signup.php');
            exit();
        }
        
        if(strlen($username) < 4){
            $_SESSION['error'] = "Username must be at least 4 characters!";
            header('location: ../Views/signup.php');
            exit();
        }
        
        if(strlen($username) > 20){
            $_SESSION['error'] = "Username must be less than 20 characters!";
            header('location: ../Views/signup.php');
            exit();
        }
        
        if(strpos($username, ' ') !== false){
            $_SESSION['error'] = "Username cannot contain spaces!";
            header('location: ../Views/signup.php');
            exit();
        }
        
        
        if($password == ""){
            $_SESSION['error'] = "Password is required!";
            header('location: ../Views/signup.php');
            exit();
        }
        
        if(strlen($password) < 6){
            $_SESSION['error'] = "Password must be at least 6 characters!";
            header('location: ../Views/signup.php');
            exit();
        }
        
        if(strlen($password) > 30){
            $_SESSION['error'] = "Password must be less than 30 characters!";
            header('location: ../Views/signup.php');
            exit();
        }
        
        
        if($confirm_password == ""){
            $_SESSION['error'] = "Confirm password is required!";
            header('location: ../Views/signup.php');
            exit();
        }
        
        if($password != $confirm_password){
            $_SESSION['error'] = "Passwords do not match!";
            header('location: ../Views/signup.php');
            exit();
        }
        
        
        $allUsers = getAllUsers();
        foreach($allUsers as $existingUser){
            if($existingUser['username'] == $username){
                $_SESSION['error'] = "Username already exists! Please choose another.";
                header('location: ../Views/signup.php');
                exit();
            }
        }
        
        // All validations passed, add user
        $user = [
            'username'=> $username,
            'password'=> $password,
            'full_name'=> $full_name,
            'user_type'=> 'user'
        ];
        
        $result = addUser($user);
        
        if($result){
            $_SESSION['success'] = "Registration successful! Please login.";
            header('location: ../Views/login.php');
            exit();
        }else{
            $_SESSION['error'] = "Registration failed! Please try again.";
            header('location: ../Views/signup.php');
            exit();
        }
    }else{
        header('location: ../Views/signup.php');
    }
?>
