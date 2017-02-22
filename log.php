<?php
use Carbon\Carbon;
require_once "connection.php";
if (isset($_POST['user_id'])) {
	if($sql = $conn->prepare("select device_id, user_id, log_time, action from log where user_id = ?")) {
		$sql->bind_param("s", $_POST['user_id']);
		$sql->execute();
		$sql->bind_result($device_id, $user_id, $log_time, $action);
		while ($sql->fetch()) {
			$log[] = array
			('lockName' => $device_id,
			'userName' => $user_id,
			'log_time' => $log_time,
			'action' => $action );
		}
		json_encode($log);
		$data = array('log' => $log );
	}	
	echo json_encode($data);
} else {
	echo $conn->error;
}
?>