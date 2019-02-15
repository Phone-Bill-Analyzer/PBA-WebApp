<?php

header("Content-type: text/json");

require( 'pba-functions.php' );

createDBConnection();

$allowedExts = array("pdf");
$temp = explode(".", $_FILES["file"]["name"]);
$extension = end($temp);

if ( (($_FILES["file"]["type"] == "application/pdf") || ($_FILES["file"]["type"] == "application/octet-stream")) 
	&& ($_FILES["file"]["size"] < 900000) 
	&& in_array($extension, $allowedExts) ) {


	if ($_FILES["file"]["error"] > 0) {
		echo "Error: " . $_FILES["file"]["error"] . "<br>";
	}
	else {
		//*
		echo "Upload: " . $_FILES["file"]["name"] . "<br>";
		echo "Type: " . $_FILES["file"]["type"] . "<br>";
		echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
		echo "Stored in: " . $_FILES["file"]["tmp_name"] . "<br>";
		//*/
	}
	
	$fileName = $_FILES["file"]["tmp_name"];
	$pwd = $_POST["password"];
	$type = $_POST["type"];
	
	$command = "java -jar ParseBill.jar $type $fileName $pwd";
	echo $command . "<br>";
	
	$jsonData = shell_exec($command);
	echo $jsonData;
	
	$bill_details = json_decode($jsonData);
	
	// Save to DB

	$error_code = $bill_details->ErrorCode;
	$error_message = $bill_details->Message;
	
	if($error_code != 0){
		echo "Error occurred.<br>";
		echo $error_message;
	}
	else{

		save_bill_details($type, $bill_details);
		//echo "File Upload Success";
	}
	
}
else {
	echo "Invalid file";
}

?> 
