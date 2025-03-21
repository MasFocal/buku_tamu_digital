<?php
    session_start();
    session_unset();
    session_destroy();
    unset($_SESSION['username']);
    header("location: ../login.php");
?>