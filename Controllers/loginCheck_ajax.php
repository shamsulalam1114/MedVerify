<?php
session_start();
require_once('../Models/userModel.php');
require_once('ajax_response.php');

if(isset($_POST['submit']) || isAjaxRequest()){
    $username = $_REQUEST['username'] ?? '';
    $password = $_REQUEST['password'] ?? '';

    if($username == "" || $password == ""){
        if(isAjaxRequest()){
            sendErrorResponse("Username and Password are required!");
        }
        $_SESSION['error'] = "Username and Password are required!";
        header('location: ../Views/login.php');
        exit();
    }

    $user = ['username'=> $username, 'password'=> $password];
    $result = login($user);
    
    if($result){
        setcookie('status', 'true', time()+3000, '/');
        $_SESSION['user_id'] = $result['user_id'];
        $_SESSION['username'] = $result['username'];
        $_SESSION['full_name'] = $result['full_name'];
        $_SESSION['user_type'] = $result['user_type'];

        if(isAjaxRequest()){
            sendSuccessResponse("Login successful!", [
                'redirect' => $result['user_type'] == 'admin' ? '../Controllers/home.php' : '../Views/calendar.php',
                'user' => [
                    'username' => $result['username'],
                    'full_name' => $result['full_name'],
                    'user_type' => $result['user_type']
                ]
            ]);
        }

        if($result['user_type'] == 'admin'){
            header('location: home.php');
        }else{
            header('location: ../Views/calendar.php');
        }
    }else{
        if(isAjaxRequest()){
            sendErrorResponse("Invalid username or password!", [], 401);
        }
        $_SESSION['error'] = "Invalid username or password!";
        header('location: ../Views/login.php');
        exit();
    }
}else{
    header('location: ../Views/login.php');
}
?>
