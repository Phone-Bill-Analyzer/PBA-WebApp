<?php

require( 'pba-functions.php' );

$sid = $_POST['session_id'];

$post_data = $_POST['data'];
$post_data = stripslashes($post_data);

if($sid ==  ""){
    die('{"ErrorCode":1,"Message":"Session does not exist. Please check your session and try again"}');
}

if($post_data ==  ""){
    die('{"ErrorCode":1,"Message":"Bill data not found"}');
}

createDBConnection();

if(!checkSessionID($sid)){
	die('{"ErrorCode":1,"Message":"Session does not exist. Please check your session and try again."}');
}

$post_data = json_decode($post_data);

$bill_meta_data = $post_data->BillMetaData;
$call_details = $post_data->CallDetails;
$contact_names = $post_data->ContactNames;
$contact_groups = $post_data->ContactGroups;

// Bill Meta Data
foreach($bill_meta_data as $bill_meta){
	
	upsertBillMetaData($sid,$bill_meta);
	
}

// Call Details
foreach($call_details as $call_detail_item){
	
	upsertCallDetailData($sid,$call_detail_item);
	
}

// Delete Old Contact Names and Groups
deleteContactInfo($sid);

// Contact Names
foreach($contact_names as $contact_name){
	
	upsertContactName($sid,$contact_name);
	
}

// Contact Groups
foreach($contact_groups as $contact_group){
	
	upsertContactGroup($sid,$contact_group);
	
}

echo '{"ErrorCode":0,"Message":"Success"}';

?>