<?php
require_once 'connection.php';
require_once "vendor/phpmailer/phpmailer/PHPMailerAutoload.php";

//get name and address of lock from get variable
$reqDeviceAddress = $_GET['address'];
$reqUserEmail = $_GET['email'];
$sendTOEmail = "prtkkhandelwal3";

//fetch user data by email 
include 'user.php';
$reqUser = getUserByEmail($reqUserEmail, $conn);

//include check device php file
include 'checkdevice.php';

//check wether device exist or not in device list and get device details
if (checkDevice($reqDeviceAddress, $conn)) {
	$sql = "Select Name, Address, RMN from Devices where Address = '$reqDeviceAddress'";
	$result = $conn->query($sql);
	while ($row = $result->fetch_assoc()) {
		$RMN = $row['RMN'];
		$reqLock['address'] = $row['Address'];
		$reqLock['name'] = ucwords(strtolower($row['Name']));
	}
	//getting data of owner of lock
	$regUser = getUserByRMN($RMN, $conn);
	// $sql = mysql_query("Select * from Users where RMN = '$RMN'");
	// echo "\n" . $regUser['userName'] . "\t". $regUser['RMN'] . "\n";
	sendRequestToServer($reqLock, $reqUser, $regUser, $conn);
	// sendMail($email, $name, $reqNumber);
	// sendNotification($fcm, $reqNumber, $name);
} else {
	echo "\ndevice is not in the list";
}


//to store all the activities in log table
function sendRequestToServer($requestedLock, $requestedUser, $registeredUser, $conn) {
	$reqLockAdd = $requestedLock['address'];
	$reqLockName = $requestedLock['name'];
	$reqUserName = $requestedUser['userName'];
	$reqFcm = $requestedUser['fcm'];
	$reqEmail = $requestedUser['email'];
	$reqRMN = $requestedUser['RMN'];

// data of owner of the lock
	$regUserName = $registeredUser['userName'];
	$regFcm = $registeredUser['fcm'];
	$regEmail = $registeredUser['email'];
	$regRMN = $registeredUser['RMN'];
	// echo "\n in send request to  server function" . $regFcm ." \t" . $regEmail . "\n";

	//sql query to insert request key data in database
	$sql = "Insert into requestKey (request_id, reqLockAdd, reqLockName, reqUserName, reqFcm, reqEmail, reqRMN, regUserName, regFcm, regEmail, regRMN, requested_at) VALUES (null,'$reqLockAdd','$reqLockName','$reqUserName','$reqFcm','$reqEmail','$reqRMN','$regUserName','$regFcm','$regEmail','$regRMN',null)";
	if ($conn->query($sql) === TRUE) {
		// echo "\nRequest added to database successfully\n";
		$lastId = $conn->insert_id;
		// echo $lastId;
		sendNotification($requestedLock, $registeredUser, $registeredUser, $lastId);
		// sendMail($reqLockName, $reqUserName, $regEmail, $regUserName);
	} else {
		echo "\nError: " . $sql . $conn->error . "\ns";
	}
}

function sendNotification($requestedLock, $requestedUser, $registeredUser, $request_id){

	//fcm of owner of lock
	$fcm = $registeredUser['fcm'];

	//notification array
	$notification = array();
	$notification['title'] = "Access Request";
	$notification['body'] = $requestedUser['userName'] . " has send you request to grant access to " . $requestedLock['name'];
	$notification['icon'] = "appicon";


	//payload array
	$data = array();
	// $data['title'] = "Lock Access Request";
	$data['reqLockName'] = $requestedLock['name'];
	$data['reqUserName'] = $requestedUser['userName'];
	$data['reqRMN'] = $requestedUser['RMN'];
	$data['request_id'] = $request_id;


	$fields = array(
		'to' => $fcm,
		'notification' => $notification,
		'data' => $data,
		);

	// echo json_encode($fields);
	include "sendFcm.php";
	sendPushNotification($fields);
}

// send mail to RMN user about the key request 
function sendMail($reqLockName, $reqUserName, $regEmail, $regUserName) {
	//send email to owner regarding access request 

	//Phpmailer object
	$mail = new Phpmailer;

	//Enable Smtp Debugging
	$mail->SMTPDebug = 3;

	//set phpmailer to use smtp
	$mail->isSMTP();

	//set smtp hostname
	$mail->Host = "smtp.gmail.com";

	//set true if smtp requires authentication to send mail
	$mail->SMTPAuth = true;

	//Provide username and password
	// include 'credentials.php';
	$mail->UserName = "keyblue17@gmail.com";
	$mail->Password = "bluekey17";

	//is smtp requires TLS encryption then set it
	$mail->SMTPSecure = "tls";

	//set tcp port to connect to
	$mail->Port = 587;

	//sender
	$mail->From = "keyblue17@gmail.com";
	$mail->FromName = "Team Blue Key";

	//Receiver mail id
	$mail->addAddress('prtkkhandelwal3@gmail.com', $regUserName);

	//address to which recipient can reply
	// $mail->addReplyTo("reply@domin.com", "Reply");

	$mail->isHtml(true);

	$mail->Subject = "Lock Access Request";
	$mail->Body = "<i> Mail body in HTML </i>";
	$mail->AltBody = "Hello " . $regUserName . ",\n" . $reqUserName . "has requested you to grant him/her access to the " . $reqLockName . ". \n If you know requested user is a trusted person then only grant him/her access. \n It is suggessted that you check wether it's genuine user or not through call. \n Thank You\n Team Blue Key";
	
	if (!$mail->send()) {
			echo "Mailer Error " . $mail->ErrorInfo;
		}else {
			echo "Mail has been sent successfully";
		}
}

//send push notification to RMN user mobile using firebase
// function sendNotification($fcm, $name, $reqNumber){

// }

// mysql_close($conn);
?>