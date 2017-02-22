<?php
require_once('connect.php');
// die("bing");
$address = $_GET['address'];
$password_input = $_GET['password'];
if(!empty($_GET['address'])) {
	$sql1 = "select * from Devices where Address = '$address'";
	$sql1 = mysql_query($sql1);
	$row = mysql_fetch_array($sql1);
	if(!empty($row)) {
		//success = 1, device registered take to login activity
		$response['success'] = 1;
		
		// $response['message'] = "Device is already registered"
		$password = $row['Password'];
		if($password_input == $password) {
			echo "True";
		} else {
			echo "False";
		}
		// $response['password'] = $password;
		// json_encode($response);
	} else {
		$response['success'] = 0;
		//device does not exist in database, register activity
		echo "error";
		// json_encode($response);
	}
} else {
	echo "string";
}
mysql_close($conn);
?>