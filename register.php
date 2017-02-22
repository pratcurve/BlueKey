<?php
require_once('connection.php');
$name = $_GET['name'];
$address = $_GET['address'];
$password = password_hash($_GET['password'], PASSWORD_DEFAULT);
$RMN = $_GET['RMN'];

if(!empty($_GET)) {
	if(empty($_GET['address']) || empty($_GET['password']) || empty($_GET['RMN'])) {
		$response['success'] = 0;
		$response['message'] =  "Please enter Password";
		$response['address'] = $address;
		$response['name'] = $name;
		// die will kill the page and will not let the code below to execute
		echo (json_encode($response));
	} else {
		include 'checkdevice.php';
		if (!checkDevice($_GET['address'])) {

			//insert device into database, if doesn't exist already in the database
			$sql = "Insert Into Devices(id, Name, Address, Password, RMN) VALUES(null, '$name', '$address', '$password', '$RMN')";
			if(mysql_query($sql, $conn)) {
			echo "Registered";
			$response['success'] = 1;
			$response['message'] = "Registered";
			$response['name'] = $name;
			$response['address'] = $address;
			// echo json_encode($response));
			echo json_encode($response);
			}  else {
			echo $conn.mysql_error();
			$response['success'] = 0;
			$response['message'] = "Error in registering the device";
			$response['name'] = $name;
			$response['address'] = $address;
			echo (json_encode($response));
			} 		
		} else {
			$response['success'] = 0;
			$response['message'] = "Device Already Registered";
			$response['name'] = $name;
			$response['address'] = $address;
			echo json_encode($response);
		}

	}
}
mysql_close($conn);
?>