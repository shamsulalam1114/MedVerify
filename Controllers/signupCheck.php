<?php
    session_start();
    require_once('../Models/userModel.php');
    
    if(isset($_POST['submit'])){
        $full_name = $_REQUEST['full_name'];
        $username = $_REQUEST['username'];
        $password = $_REQUEST['password'];
        $confirm_password = $_REQUEST['confirm_password'];
        
        if($full_name == ""){
            echo "Full name is required!";
        }else if($username == ""){
            echo "Username is required!";
        }else if($password == ""){
            echo "Password is required!";
        }else if($confirm_password == ""){
            echo "Confirm password is required!";
        }else if($password != $confirm_password){
            echo "Passwords do not match!";
        }else{
            
            $user = [
                'username'=> $username,
                'password'=> $password,
                'full_name'=> $full_name,
                'user_type'=> 'user'
            ];
            
            $result = addUser($user);
            
            if($result){
                header('location: ../Views/login.php?msg=registered');
            }else{
                echo "Registration failed! Username may already exist.";
            }
        }
    }else{
        header('location: ../Views/signup.php');
    }
?>
