<?php

session_start(); //Get session details
unset($_SESSION['name']); //unset session variables
unset($_SESSION['memberID']);

session_destroy(); //destroy session

header("Location: ../index.php"); //direct back to home page
exit;
?>