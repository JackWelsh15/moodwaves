<?php
session_start();

if(isset($_SESSION['id'])) {
    echo "Welcome <strong>".$_SESSION['id']."</strong><br/>";
} else {
    header('location:../public/index.php');
}

?>
<a href="logout.php">Logout</a>