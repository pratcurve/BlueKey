<?php
define("GOOGLE_API_KEY", "AAAAWcnPsls:APA91bHmFt8ltxA3Occ2l_DwNF3Cpqel1EPpCbYowT-P8WxwX68WKtNAF6phRioCD3AgAVgENLAPE84tAPrWxIX6e7cVFEsLM8vPa7hvpRaFGGluouetzl5eToMz5VASbSgm1WyveKNo");

function sendPushNotification($fields){
	echo json_encode($fields);
	$url = 'https://fcm.googleapis.com/fcm/send';
	$headers = array(
		'Authorization: key=' . GOOGLE_API_KEY,
		'Content-Type: application/json'
		);

	$ch = curl_init();
	//set url, number of post vars
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    // curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V6 );

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Disabling SSL Certificate support temporarly
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);
    if ($result == false) {
    	echo "curl failed";
    	// die(curl_error($ch));
    } else {
    	echo "\ndone";
    }
    curl_close($ch);
}
?>