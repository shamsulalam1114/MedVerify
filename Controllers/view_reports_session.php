<?php
    session_start();

    if(isset($_COOKIE['status']) !== true){
        header('location: ../Views/login.php');
    }

    //print_r($_SERVER);
?>
