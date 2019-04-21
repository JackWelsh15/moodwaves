<?php
include ('functions.php');

// mysqli_connect() function opens a new connection to the MySQL server.
$db = getConnection();
session_start();// Starting Session
// Storing Session
$user_check = $_SESSION['id'];
// SQL Query To Fetch Complete Information Of User
$sql = "SELECT email from members where email = '$user_check'";
$ses_sql = mysqli_query($db, $sql);
$row = mysqli_fetch_assoc($ses_sql);
$login_session = $row['email'];
