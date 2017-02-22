<?php
require_once "connection.php";
include 'user.php';
header('Content-type: application/json');

if (isset($_POST['email']) && isset($_POST['password'])) {
	$promptEmail = $_POST['email'];
	$promtPassword = $_POST['password'];
		if (checkUserByEmail($promptEmail, $conn)) {
			if ($sql = $conn->prepare("Select user_name, RMN, email, password from User where email = ? limit 1")) {
		# code...
				$sql->bind_param("s", $promptEmail);
				$sql->execute();
				$sql->bind_result($userName, $userRMN, $userEmail, $userPassword);
				while ($sql->fetch()) {
					if (password_verify($promtPassword, $userPassword)) {
						$response = ["success" => "1", "error" => "successfully loggedIn"];
						echo json_encode($response);
					} else {
						$response = ["success" => "0", "error" => "Incorrect Password"];
						echo json_encode($response);
					}
				}	
		
			} else {
				$response = ["success" => "0", "error" => "Error in connection"];
				echo json_encode($response);
			}
		} else {
			$response = ["success" => "0", "error" => "Email is not registered"];
			echo json_encode($response);
		}
} else {
	$response = ["success" => "0", "error" => "Please try again"];
	echo json_encode($response);
}

?>