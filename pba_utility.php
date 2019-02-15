<?php

require( 'pba-functions.php' );

sessionStart();
$sid = getSessionID();

createDBConnection();

$code = $_POST['code'];

if($code == "Get-Bill-List"){
	echo get_bill_list($sid );
}

if($code == "Get-Session-ID"){
	echo $sid;
}

if($code == "Bill-Summary-Data"){
	
	$bill_id = $_POST['bill_id'];
	echo get_bill_summary_data($sid,$bill_id);
}

if($code == "Itemized-Bill-Details"){
	
	$bill_id = $_POST['bill_id'];
	echo get_itemized_bill_details($sid,$bill_id);
}

if($code == "Top-Caller-Details"){
	
	$bill_id = $_POST['bill_id'];
	echo get_top_caller_details($sid,$bill_id);
}

if($code == "Data-Usage-Details"){
	
	$bill_id = $_POST['bill_id'];
	echo get_data_usage_details($sid,$bill_id);
}
?>