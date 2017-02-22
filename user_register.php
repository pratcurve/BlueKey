<?php
require_once 'vendor/autoload.php';
use Carbon\Carbon;
require_once 'connection.php';
header('Content-type: application/json');
include 'user.php';


$response = array(
	'success' => 0);

if (isset($_POST['username']) && isset($_POST['email'])) {
	$promptUsername = $_POST['username'];
	$promptEmail = $_POST['email'];
	$promptPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
	$promptRMN = $_POST['RMN'];
	$fcm = $_POST['fcm'];
	if (!checkUserByEmail($promptEmail, $conn)) {
		if (!checkRMN($promptRMN, $conn)) {
			if ($sql = $conn->prepare("insert into User (user_name, RMN, email, password, fcm) values (?,?,?,?,?)")) {
					# code...
				$sql->bind_param("sssss", $promptUsername, $promptRMN, $promptEmail, $promptPassword, $fcm);
				$sql->execute();
				$sql->close();
				$response = ["success" => "1"];
				echo json_encode($response);
				} else {
					$response = ["success" => "0", "error" => "Please try again!"];
			}
		} else {
			$response = ["success" => "0", "error" => "Number already registered"];
			echo json_encode($response);
		}
	} else {
		$response = ["success" => "0", "error" => "Email already registered"];
		echo json_encode($response);
	}
} else {
	$response = ["success" => "0", "error" => "Error in connecting"];
	echo json_encode($response);
}



?>