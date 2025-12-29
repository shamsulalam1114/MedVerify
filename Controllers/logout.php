<?php

    setcookie('status', 'true', time()-10, '/');
    header('location: ../Views/login.php');

?>