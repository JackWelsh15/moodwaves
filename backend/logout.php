<?php
session_start();
unset($_SESSION['name']);
unset($_SESSION['memberID']);

session_destroy();

header("Location: ../public/index.php");
exit;
?>