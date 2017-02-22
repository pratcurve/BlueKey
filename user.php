<?php
require_once 'connection.php';
header('Content-type: application/json');

function getUserByEmail($email, $conn) {
	
	if ($sql = $conn->prepare("Select user_name, RMN, fcm, email from User where email = ?")) {
		$sql->bind_param("s", $email);
		$sql->execute();
		$sql->bind_result($userName, $RMN, $fcm, $email);
		$sql->store_result();
		$rows = $sql->num_rows;
		if ($rows > 0) {
			while ($sql->fetch()) {
				echo "\n" . $userName . $RMN . $email;
				$user['userName'] = ucwords(strtolower($userName));
				$user['RMN'] = $RMN;
				$user['fcm'] = $fcm; 
				$user['email'] = $email;
				return $user;
			}
		} 			
	}
}

function getUserByRMN($RMN, $conn) {
	// echo "\n". $RMN;
	$sql = "Select * from User where RMN = '$RMN'";
	$result = $conn->query($sql);
	while ($row = $result->fetch_assoc()) {
		$user['userName'] = ucwords(strtolower($row['user_name']));
		$user['email'] = $row['email'];
		$user['fcm'] = $row['fcm'];
		$user['RMN'] = $RMN;
		// echo  "\n" . $user['userName'] . $user['email'] . $user['fcm'] . "\n";
		// return $user;
	}
	return $user;
}

function getUserById($user_id, $conn) 
{
	if ($sql = $conn->prepare("Select user_name from User where user_id = ? limit 1")) {
		$sql->bind_param('s', $user_id);
		$sql->execute();
		$sql->bind_result($userName);
		while ($sql->fetch()) {
			return $userName;
		}
	}
}

function checkUserByEmail($email, $conn) {
	if ($sql = $conn->prepare("select user_name from User where email = ?")) {
		$sql->bind_param("s", $email);
		$sql->execute();
		$sql->bind_result($userName);
		$sql->store_result();
		$rows = $sql->num_rows;
		if ($rows > 0) {
			return true;
		} 		# code...
	}
	return false;
}

function checkRMN($RMN, $conn) {
	if ($sql = $conn->prepare("select user_name from User where RMN = ?")) {
		$sql->bind_param('s', $RMN);
		$sql->execute();
		$sql->bind_result($userName);
		$sql->store_result();
		$rows = $sql->num_rows;
		if ($rows > 0 ) {
			return true;
		}
		return false;
	}
}
// mysql_close();
?>