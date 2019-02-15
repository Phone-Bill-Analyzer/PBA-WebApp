<?php

header("Content-type: text/json");

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
		/*
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
	//echo $command . "<br>";
	
	$jsonOutput = shell_exec($command);
	echo $jsonOutput;
	
	if(strcmp($pwd,"") !== 0){
		$fileName = $fileName."_".$pwd;
	}
	
move_uploaded_file($fileName, "temp_files".$fileName);

}
else {
	echo "Invalid file.";
	echo $_FILES["file"]["type"];
	echo $_FILES["file"]["size"];
	echo $_FILES["file"]["name"];
	echo "Done";
	//var_dump($_FILES);
}

?> 
