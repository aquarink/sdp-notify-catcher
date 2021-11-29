<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

function uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),

        // 16 bits for "time_mid"
        mt_rand(0, 0xffff),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand(0, 0x0fff) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand(0, 0x3fff) | 0x8000,

        // 48 bits for "node"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

$jsondata = file_get_contents("php://input");

if($jsondata != '') {

	$uuid 			= uuid();
	$arr 			= json_decode($jsondata);
	$txId 			= $arr->txId;
	$subscriberId 	= $arr->subscriberId;
	$programId 		= $arr->programId;
	$sid 			= $arr->sid;
	$channel 		= $arr->channel;
	$purpose 		= $arr->purpose;
	$result 		= $arr->result;
	$contentMO 		= $arr->contentMO;


	$reply = array(
		"message" 			=> "Reply test CP xxx", 
		"subscriberId" 		=> $subscriberId , 
		"programId" 		=> $programId, 
		"sid"				=> $sid, 
		"appId" 			=> "xxx", 
		"appPwd"			=> "xxx", 
		"transactionId" 	=> $uuid, 
		"registered" 		=> "yes" 
	);

	$reply_encode = json_encode($reply);

	// POST
	
	$url = "http://10.44.7.5:80/partner-sms/pushmt";
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $reply_encode);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$res = curl_exec($ch);
	$err = curl_error($ch);
	curl_close($ch);

	if($err) {
	 	$pesan = $err;
	} else {
	  	$pesan = $res;
	}

	echo 'OK';
} else {
	echo 'NOK';
}
?>