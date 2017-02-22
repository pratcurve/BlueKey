<?php
require_once('connection.php');
function checkDevice($address, $conn) {
	if(!empty($address)) {
		$sql = "select * from Devices where Address = '$address'";
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			$response = true;
			// echo "\n response is true";
		} else {
			$response = false;
			// echo "\n response is false";
		}
	} else {
		echo "string";
	}
return $response;
}

function getDeviceById($device_id, $conn)
{
	$device = array();
	if ($sql = $conn->prepare('Select Name, RMN from Device where device_id = ? Limit 1')) {
		$sql->bind_param('s', $device_id);
		$sql->execute();
		$sql->bind_result($lockName, $RMN);
		while ($sql->fetch()) {
			$device['lockName'] = $lockName;
			$device['RMN'] = $RMN;
		}
	}
	return $device;
}
?>