<?php

// Author: Jack Welsh
//
// Function to get database connection.

function getConnection(){
$username = 'unn_w18020302';
$password ='QUEYQ0YY';
$host = 'newnumyspace.co.uk'
$dbname ='saved_songs'

$db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
return $db;
}

 ?>
