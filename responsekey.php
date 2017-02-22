<?php
require_once 'vendor/autoload.php';
use Carbon\Carbon;

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "connection.php";
if (!empty($_GET)) {
	$request_id = $_GET['request_id'];
	$permissionGranted = $_GET['permission'];	
}

//permission = 1 is permission granted; 
//permission = 0 is permission not granted;
//check wether permission is granted or not and perform action
if ($permissionGranted == 1) {
	$string = 'abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$shuffledString = str_shuffle($string);
	$OTP = substr($shuffledString, 1, 7);
	$generated_on = Carbon::now();
	$expires_on = Carbon::now()->addMinutes(2);
	$sql = $conn->prepare("INSERT INTO otp (otp, request_id, generated_on, expires_on) VALUES (?,?,?,?)");
	$sql->bind_param("ssss", $OTP, $request_id, $generated_on, $expires_on);
	$sql->execute();
	// $lastID = $conn->insert_id;
	print($sql->error);
	$sql->close();
	sendOTP($conn, $request_id);
} else {
	echo "permission not granted";
}

function sendOTP($conn, $request_id){
	// echo "sendOTP";
	$sql = $conn->prepare("Select otp_id, otp, request_id, generated_on, expires_on from otp where request_id = ? Order by otp_id desc Limit 1");
	$sql->bind_param("s", $request_id);
	$sql->execute();
	$sql->bind_result($otp_id, $otp, $request_id, $generated_on, $expires_on);
		while($sql->fetch()) {
			$otp = array(
				'otp_id' => $otp_id,
				'otp' => $otp,
				'request_id' => $request_id,
				'generated_on' => $generated_on,
				'expires_on' => $expires_on,
				);
		}
		$request = fetchRequest($request_id, $conn);
		sendNotification($otp, $request);
		
	$sql->close();
}

function fetchRequest($request_id, $conn) {
	$sql = $conn->prepare("Select reqLockName, reqFcm, reqRMN, regUserName from requestKey where request_id = ? Limit 1");
		$sql->bind_param('s', $request_id);
		$sql->execute();
		$sql->bind_result($reqLockName, $reqFcm, $reqRMN, $regUserName);
		while ($sql->fetch()) {
			$request = array(
				'request_id' => $request_id,
				'reqLockName' => $reqLockName,
				'reqFcm' => $reqFcm,
				'reqRMN' => $reqRMN,
				'regUserName' => $regUserName,
				);
		}
		return $request;
}

function sendNotification($otp, $request){
	
	//fcm of the requested user
	$fcm = $request['reqFcm'];

	//notification array
	$notification = array(
		'title' => "Access Granted",
		'body' => "Access to lock ". $request['reqLockName']. " has been granted. OTP has been send to your notification box in app",
		'icon' => "appicon",
		);

	$data = array(
		'request_id' => $request['request_id'],
		'OTP' => $otp['otp'],
		'generated_on' => $otp['generated_on'],
		'expires_on' => $otp['expires_on'],
		'reqLockName' => $request['reqLockName'],
		'regUserName' => $request['regUserName'],
		);

	$fields = array(
		'to' => $fcm,
		'notification' => $notification,
		'data' => $data,
		);

	// echo json_encode($fields);

	//include push notification to fcm file
	include 'sendFcm.php';
	sendPushNotification($fields);
}
?>