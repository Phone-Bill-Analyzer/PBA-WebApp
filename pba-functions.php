<?php

require_once( 'config.php' );

function sessionStart(){

	session_start();
		
	$sid = $_SESSION["session_id"];
	//echo "Session ID: ".$sid;
		
	if(strcmp($sid,"") == 0){
		$sid = rand (1000, 9999);
		while(checkSessionID($sid)){
			$sid = rand (1000, 9999);
		}
		$_SESSION["session_id"] = $sid;
	}
	
	// Update Session List.
	updateSessionList($sid);
}

function getSessionID(){
	
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	
	$sid = $_SESSION["session_id"];
	return $sid;
}

function checkSessionID($sid){
	
	global $linkID;
	
	if($linkID == null){
		createDBConnection();
	}
	
	$sql = "select * from SessionList where SessionID = $sid";
	$result = mysqli_query($linkID,$sql);
	if($row = mysqli_fetch_assoc($result)){
		return true;
	}
	else{
		return false;
	}
	
}

function updateSessionList($sid){
	
	global $linkID;
	
	if($linkID == null){
		createDBConnection();
	}
	
	$ip = $_SERVER['REMOTE_ADDR'];
	
	$sql = "INSERT INTO SessionList (SessionID,IP) VALUES ($sid,'$ip') ON DUPLICATE KEY UPDATE LastAccess = current_timestamp";
	$result = mysqli_query($linkID,$sql);
	
}

function createDBConnection(){
	
	global $linkID;
	$host = DB_HOST;
	$user = DB_USER;
	$pass = DB_PASSWORD;
	$database = DB_NAME;

	$linkID = mysqli_connect($host, $user, $pass) or die("Could not connect to host.");
	mysqli_select_db($linkID,$database) or die("Could not find database.");

}

function upsertBillMetaData($sid,$bill_meta){
	
	global $linkID;
	
	$bill_no = $bill_meta->BillNo;
	$phone_no = $bill_meta->PhoneNumber;
	$type = $bill_meta->BillType;
	$bill_date = date('Y-m-d', strtotime($bill_meta->BillDate));
	$from_date = date('Y-m-d', strtotime($bill_meta->FromDate));
	$to_date = date('Y-m-d', strtotime($bill_meta->ToDate));
	$due_date = date('Y-m-d', strtotime($bill_meta->DueDate));
	
	$sql = "SELECT BillNo from BillMetaData where SessionID = $sid and BillNo = '$bill_no'";
	$result = mysqli_query($linkID,$sql);
	
	if($row = mysqli_fetch_assoc($result)){
		// We have an entry. Delete Bill Data
		deleteBillData($sid,$bill_no);		
	}
	
	$sql = "INSERT INTO BillMetaData (SessionID, BillNo, BillType, PhoneNo, BillDate, FromDate, ToDate, DueDate) ".
			"VALUES ($sid, '$bill_no','$type','$phone_no','$bill_date','$from_date','$to_date','$due_date')";
	
	//echo $sql;
	$result = mysqli_query($linkID,$sql);
	
}

function deleteBillData($sid,$bill_no){

	global $linkID;
	
	$sql = "DELETE from BillMetaData where SessionID = $sid and BillNo = '$bill_no'";
	$result = mysqli_query($linkID,$sql);
	
	$sql = "DELETE from BillCallDetails where SessionID = $sid and BillNo = '$bill_no'";
	$result = mysqli_query($linkID,$sql);
	
}

function upsertCallDetailData($sid,$call_detail_item){
	
	global $linkID;
	
	$bill_no = $call_detail_item->BillNo;
	
	$call_date_time = strtotime($call_detail_item->callDate." ".$call_detail_item->callTime);

	$phone_no = $call_detail_item->phoneNumber;
	$call_date = date('Y-m-d', $call_date_time);
	$call_time = date('H:i:s', $call_date_time);
	$call_duration = $call_detail_item->duration;
	$amount = $call_detail_item->cost;
	$call_dir = $call_detail_item->callDirection;
	$comments = $call_detail_item->comments;
	$free_call = $call_detail_item->freeCall;
	$roaming_call = $call_detail_item->roamingCall;
	$sms_call = $call_detail_item->smsCall;
	$std_call = $call_detail_item->stdCall;
	$pulse = $call_detail_item->pulse;
	
	$sql = "INSERT INTO BillCallDetails (SessionID, BillNo, PhoneNo, CallDate, CallTime, CallDuration, Amount, CallDirection, Comments, IsFreeCall, IsRoaming, IsSMS, IsSTD, Pulse) ".
		"VALUES ($sid, '$bill_no','$phone_no','$call_date','$call_time','$call_duration',$amount,'$call_dir','$comments','$free_call','$roaming_call','$sms_call','$std_call','$pulse')";
	
	//echo $sql;
	$result = mysqli_query($linkID,$sql);
	
}

function deleteContactInfo($sid){
	
	global $linkID;
	
	$sql = "DELETE from ContactNames where SessionID = $sid";
	$result = mysqli_query($linkID,$sql);
	
	$sql = "DELETE from ContactGroups where SessionID = $sid";
	$result = mysqli_query($linkID,$sql);
	
}

function upsertContactName($sid,$contact_name){
	
	global $linkID;
	
	$phone_no = $contact_name->PhoneNo;
	$name = $contact_name->Name;
	
	$sql = "INSERT INTO ContactNames (SessionID, PhoneNo, Name) VALUES ($sid, '$phone_no','$name')";
		
	//echo $sql;
	$result = mysqli_query($linkID,$sql);
}

function upsertContactGroup($sid,$contact_group){
	
	global $linkID;
	
	$phone_no = $contact_group->PhoneNo;
	$name = $contact_group->GroupName;
	
	$sql = "INSERT INTO ContactGroups (SessionID, PhoneNo, GroupName) VALUES ($sid, '$phone_no','$name')";
		
	//echo $sql;
	$result = mysqli_query($linkID,$sql);
}

function save_bill_details($type, $bill_details){

	$bill_meta = $bill_details->BillDetails;
	$bill_meta->BillType = $type;
	
	$sid = getSessionID();
	
	// Save Bill Meta Data
	upsertBillMetaData($sid,$bill_meta);
	
	$call_details = $bill_details->CallDetails;
	
	foreach($call_details as $call_detail_item){
	
		 $call_detail_item->BillNo = $bill_meta->BillNo;
		 
		 // Save Call Item Data
		upsertCallDetailData($sid,$call_detail_item);
		
	}
	
	// Select Bill List and return
	$output = get_bill_list($sid);
	
	echo $output;
}

function get_bill_list($sid){
	
	global $linkID;
	
	$sql = "select BillNo, BillDate from BillMetaData where SessionID = " . $sid . " order by BillDate";
	//echo $sql;
	
	$output = "";
	$result = mysqli_query($linkID,$sql);
	$index = 0;
	
	while($row = mysqli_fetch_assoc($result)) {
		
		$val = "Bill No : " . $row['BillNo'] . " | Bill Date : " . $row['BillDate'];
		
		if($index == 0){
			$output = $val;
		}
		else{
			$output = $output.";".$val;
		}
		
		$index++;
	}
	
	return $output;
	
}

function get_bill_summary_data($sid,$bill_id){

	global $linkID;
	
	$sql = "SELECT case when cn.Name is null then cd.PhoneNo else cn.Name end as Name, sum(cd.Amount) as amt ".
			"FROM BillCallDetails as cd left outer join ContactNames as cn on cd.SessionID = cn.SessionID and cd.PhoneNo = cn.PhoneNo ".
			"where cd.SessionID = $sid and cd.BillNo = '$bill_id' and cd.PhoneNo <> 'data' ".
			"group by cd.PhoneNo order by amt desc limit 5";
	//echo $sql;
	$top_call_result = mysqli_query($linkID,$sql);
	
	$sql = "SELECT sum(Amount) as amt FROM BillCallDetails where SessionID = $sid and BillNo = '$bill_id'";
	//echo $sql;
	$total_amt = mysqli_query($linkID,$sql);
	
	$sql = "select (sum(CallDuration))/1024 as data_usg, sum(Amount) as data_bill from BillCallDetails where SessionID = $sid and BillNo = '$bill_id' and PhoneNo = 'data'";
	//echo $sql;
	$data_usage = mysqli_query($linkID,$sql);
	
	$sql = "select case when cg.GroupName is null then 'Others' else cg.GroupName end as GroupName, sum(cd.Amount) as Amount ".
			"from BillCallDetails as cd left outer join (select distinct PhoneNo, GroupName from ContactGroups where SessionID = $sid) as cg ".
			"on cd.PhoneNo = cg.PhoneNo ".
			"where cd.SessionID = $sid and cd.BillNo = '$bill_id' group by GroupName order by Amount desc";
	//echo $sql;
	$grp_summary = mysqli_query($linkID,$sql);
	
	$output = array();
	$output['top_5_callers'] = array();
	$output['group_summary'] = array();
	
	$index = 0;
	while($row = mysqli_fetch_assoc($top_call_result)) {
	
		if($index == 0){
			$output['top_caller'] = $row['Name'];
		}
		
		$output['top_5_callers'][] = $row;
		
		$index++;
	}
	
	while($row = mysqli_fetch_assoc($grp_summary)) {
		$output['group_summary'][] = $row;
	}
	
	$row = mysqli_fetch_assoc($total_amt);
	$output['total_amount'] = round($row['amt'],2);
	
	$row = mysqli_fetch_assoc($data_usage);
	$output['data_usage'] = round($row['data_usg'],2);
	$output['data_bill'] = round($row['data_bill'],2);
	
	return json_encode($output);
}

function get_itemized_bill_details($sid,$bill_id){

	global $linkID;
	
	$sql = "SELECT case when cn.Name is null then cd.PhoneNo else cn.Name end as Name, cd.CallDate, cd.CallTime, cd.CallDuration, cd.Amount, ".
			"cd.CallDirection, cd.IsFreeCall, cd.IsRoaming, cd.IsSMS, cd.IsSTD ".
			"FROM BillCallDetails as cd left outer join ContactNames as cn on cd.SessionID = cn.SessionID and cd.PhoneNo = cn.PhoneNo ".
			"where cd.SessionID = $sid and cd.BillNo = '$bill_id' and cd.PhoneNo <> 'data' order by cd.CallDate, cd.CallTime";
	
	//echo $sql;
	$result = mysqli_query($linkID,$sql);
	
	$output = array();
	while($row = mysqli_fetch_assoc($result)) {
		$output[] = $row;
	}
	
	return json_encode($output);
}

function get_top_caller_details($sid,$bill_id){

	global $linkID;
	
	$sql = "SELECT cd.PhoneNo , sum(cd.Amount) as amt FROM BillCallDetails as cd  where cd.SessionID = $sid and cd.BillNo = '$bill_id' and cd.PhoneNo <> 'data' group by cd.PhoneNo order by amt desc limit 1";
	
	//echo $sql;
	$top_call_result = mysqli_query($linkID,$sql);
	
	while($row = mysqli_fetch_assoc($top_call_result)) {
	
		$phone_no = $row['PhoneNo'];
	}
	
	$sql = "SELECT case when cn.Name is null then cd.PhoneNo else cn.Name end as Name, cd.CallDate, cd.CallTime, cd.CallDuration, cd.Amount, ".
			"cd.CallDirection, cd.IsFreeCall, cd.IsRoaming, cd.IsSMS, cd.IsSTD ".
			"FROM BillCallDetails as cd left outer join ContactNames as cn on cd.SessionID = cn.SessionID and cd.PhoneNo = cn.PhoneNo ".
			"where cd.SessionID = $sid and cd.BillNo = '$bill_id' and cd.PhoneNo = '$phone_no' order by cd.CallDate, cd.CallTime";
	
	//echo $sql;
	$result = mysqli_query($linkID,$sql);
	
	$output = array();
	while($row = mysqli_fetch_assoc($result)) {
		$output[] = $row;
	}
	
	return json_encode($output);
}

function get_data_usage_details($sid,$bill_id){

	global $linkID;
	
	$sql = "SELECT case when cn.Name is null then cd.PhoneNo else cn.Name end as Name, cd.CallDate, cd.CallTime, (cd.CallDuration/1024) as Volume, cd.Amount, ".
			"cd.CallDirection, cd.IsFreeCall, cd.IsRoaming, cd.IsSMS, cd.IsSTD ".
			"FROM BillCallDetails as cd left outer join ContactNames as cn on cd.SessionID = cn.SessionID and cd.PhoneNo = cn.PhoneNo ".
			"where cd.SessionID = $sid and cd.BillNo = '$bill_id' and cd.PhoneNo = 'data' order by cd.CallDate, cd.CallTime";
	
	//echo $sql;
	$result = mysqli_query($linkID,$sql);
	
	$output = array();
	while($row = mysqli_fetch_assoc($result)) {
		$output[] = $row;
	}
	
	return json_encode($output);
}

function delete_old_sessions(){
	
	global $linkID;
	
	// Delete session older than 1 day
	$sql = "DELETE FROM `SessionList` WHERE (current_timestamp - LastAccess) / (60*60*24) > 1";
	
	//echo $sql;
	$result = mysqli_query($linkID,$sql);
	
}
?>