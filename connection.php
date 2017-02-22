<?php
$servername = "localhost";
$username = "root";
$password = "2335171";
$database = "home_automation";

//Create Connection
$conn = new mysqli($servername, $username, $password, $database);

//check connection error
if ($conn->connect_error) {
	echo json_encode($conn->error);
} 
?>