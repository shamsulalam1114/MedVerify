<?php
    session_start();
    require_once('../Models/userModel.php');
    
    if(isset($_POST['submit'])){
        $full_name = $_REQUEST['full_name'];
        $username = $_REQUEST['username'];
        $password = $_REQUEST['password'];
        $confirm_password = $_REQUEST['confirm_password'];
        
        if($full_name == ""){
            $_SESSION['error'] = "Full name is required!";
            header('location: ../Views/signup.php');
            exit();
        }else if($username == ""){
            $_SESSION['error'] = "Username is required!";
            header('location: ../Views/signup.php');
            exit();
        }else if($password == ""){
            $_SESSION['error'] = "Password is required!";
            header('location: ../Views/signup.php');
            exit();
        }else if($confirm_password == ""){
            $_SESSION['error'] = "Confirm password is required!";
            header('location: ../Views/signup.php');
            exit();
        }else if($password != $confirm_password){
            $_SESSION['error'] = "Passwords do not match!";
            header('location: ../Views/signup.php');
            exit();
        }else{
            
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
                $_SESSION['error'] = "Registration failed! Username may already exist.";
                header('location: ../Views/signup.php');
                exit();
            }
        }
    }else{
        header('location: ../Views/signup.php');
    }
?>
