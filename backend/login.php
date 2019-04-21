<?php
include('functions.php');

//session_start(); // Starting Session
//$error = 'Session Failed'; // Variable To Store Error Message
//if (isset($_POST['submit'])) {
//    if (empty($_POST['email']) || empty($_POST['password'])) {
//        $error = "Email Address or Password is invalid";
//    } else {
//        // Define $username and $password
//        $username = $_POST['email'];
//        $password = $_POST['password'];
//        // getConnection() function opens a new connection to the MySQL server.
//        $db = getConnection();
//        // SQL query to fetch information of registerd users and finds user match.
//        $query = "SELECT email, password from members where username=? AND password=? LIMIT 1";
//        // To protect MySQL injection for Security purpose
//        $stmt = $db->prepare($query);
//        $stmt->bind_param("ss", $username, $password);
//        $stmt->execute();
//        $stmt->bind_result($username, $password);
//        $stmt->store_result();
//        if ($stmt->fetch()) { //fetching the contents of the row
//            $_SESSION['login_user'] = $username; // Initializing Session
//            header("location: ../public/myplaylist.php"); // Redirecting To Profile Page
//        }
//        mysqli_close($db); // Closing Connection
//    }
//}
//?>