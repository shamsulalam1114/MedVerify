<?php
    session_start();

    if(isset($_COOKIE['status']) !== true){
        header('location: ../Views/login.php');
        exit();
    }
    
    if(!isset($_SESSION['username'])){
        header('location: ../Views/login.php');
        exit();
    }

    //print_r($_SERVER);
?>
