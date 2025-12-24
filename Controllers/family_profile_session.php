<?php
    session_start();

    if(isset($_SESSION['status']) !== true){
        header('location: ../Views/login.php');
    }

?>
