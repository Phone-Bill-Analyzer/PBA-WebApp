<?php

header("Content-type: text/json");

$allowedExts = array("pdf");
$temp = explode(".", $_FILES["file"]["name"]);
$extension = end($temp);

if ( (($_FILES["file"]["type"] == "application/pdf") || ($_FILES["file"]["type"] == "application/octet-stream")) 
	&& ($_FILES["file"]["size"] < 400000) 
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
	
	$command = "java -jar PDFReader.jar $fileName $pwd";
	//echo $command . "<br>";
	
	$jsonOutput = shell_exec($command);
	echo $jsonOutput;
	
}
else {
	echo "Invalid file";
}

?> 
