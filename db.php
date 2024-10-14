<?php
// db.php

$servername = ""; 
$username = "root"; 
$password = ""; 
$dbname = "student_portal"; 

// Create connection
$link = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$link) {
    die("Connection failed: " . mysqli_connect_error());
} 
// else {
//     // echo "Connected successfully to the database: $dbname";
// }

// Optionally set character set
mysqli_set_charset($link, "utf8");
