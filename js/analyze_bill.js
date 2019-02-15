$(document).ready(function(){

	$("#upload-message-danger").hide();
	
	// Default the values of Phone bills from DB
	$.post("pba_utility.php", {code:"Get-Bill-List"},
		function(data,status){
			
			var bill_list = data.split(";");
					
			var $sel_list = $("#bill-list");
			$sel_list.empty();
			
			$sel_list.append($("<option></option>")
				 .attr("value", "Select").text("Select Bill for Analysis"));
			
			for(i=0; i<bill_list.length; i++){
				
				var bill_id_text = bill_list[i];
				var bill_id = bill_id_text.split("|")[0].split(":")[1];
				bill_id = bill_id.replace(/ /g, "");
				$sel_list.append($("<option></option>")
				 .attr("value", bill_id).text(bill_id_text));
				
			}
			
	});

	// Upload a new Bill
	$("#upload-phone-bill").click(function(){
	
		var bill_type = $( "#bill-type" ).val();
		
		var filename = $("#phone-bill").prop('files')[0];
		var form_data = new FormData();
		form_data.append('file', filename);
		form_data.append('type', bill_type);
		form_data.append('password', "");
		
		$("#upload-message-danger").show();
		$("#upload-message-success").empty();
		
		$.ajax({
                url: 'parse_and_upload_bill.php', // point to server-side PHP script 
                dataType: 'text',  // what to expect back from the PHP script, if anything
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,                         
                type: 'post',
                success: function(data){
					
					var bill_list = data.split(";");
					
					var $sel_list = $("#bill-list");
					$sel_list.empty();
					
					$sel_list.append($("<option></option>")
					.attr("value", "Select").text("Select Bill for Analysis"));
					
					for(i=0; i<bill_list.length; i++){
						
						var bill_id_text = bill_list[i];
						var bill_id = bill_id_text.split("|")[0].split(":")[1];
						bill_id = bill_id.replace(/ /g, "");
						$sel_list.append($("<option></option>")
						 .attr("value", bill_id).text(bill_id_text));
						
					}
					
					$("#upload-message-danger").hide();
					$("#upload-message-success").text("Done !");
				}
		});
		
	});
	
	// Select a Bill for Analysis
	$("#bill-list").on('change', function (e) {
		
		var optionSelected = $("option:selected", this);
		var valueSelected = optionSelected.val();
		
		$.post("pba_utility.php", {code:"Bill-Summary-Data", bill_id:valueSelected},
		function(data,status){
			
			var result = JSON.parse(data);
			//$("#upload-phone-bill-area").append(data);
			$("#top_caller_name").text(result.top_caller);
			$("#total_call_amt").text(result.total_amount);
			$("#data_usage").text(result.data_usage + " MB");
			
			// Draw Top5 Chart
			var data = new google.visualization.DataTable();
			data.addColumn('string', 'Name');
			data.addColumn('number', 'Amount');
			
			var jsonData = result.top_5_callers;
			for(var i=0; i< jsonData.length; i++){
				var amt = Math.round(jsonData[i].amt * 100) / 100;
				data.addRow([jsonData[i].Name,amt]);
			}
			
			var options = {
				pieHole: 0.2,
			};

			var chart = new google.visualization.PieChart(document.getElementById('top-5-callers'));
			chart.draw(data, options);
			
			// Draw Bar chart for contact groups
			var group_data = new google.visualization.DataTable();
			group_data.addColumn('string', 'Group');
			group_data.addColumn('number', 'Amount');
				
			var groupJsonData = result.group_summary;
				
			for(var i=0; i< groupJsonData.length; i++){
				var amt = Math.round(groupJsonData[i].Amount * 100) / 100;
				group_data.addRow([groupJsonData[i].GroupName,amt]);
			}
								
			var col_chart_options = { 	legend:{position:'in'},
										hAxis:{title:'Group'}
									};

			var bar_chart = new google.visualization.ColumnChart(document.getElementById('call-amt-by-group'));
			bar_chart.draw(group_data,col_chart_options);
			
		});

	});
	
	$("#top-caller-details").click(function(){
		
		var bill_id = $( "#bill-list" ).val();
		if(bill_id === "Select"){
			return;
		}
		
		$.post("pba_utility.php", {code:"Get-Top-Caller-Details", bill_id:bill_id},
		function(data,status){
		
		
		
		});
		
	});
	
	$("#itemized-bill-details").click(function(){
		
		var bill_id = $( "#bill-list" ).val();
		if(bill_id === "Select"){
			return;
		}
		
		$.post("pba_utility.php", {code:"Itemized-Bill-Details", bill_id:bill_id},
		function(data,status){
		
			var jsonData = JSON.parse(data);
			var data_table = new google.visualization.DataTable();
			
			data_table.addColumn('string', 'Name');
			data_table.addColumn('string', 'Call Date');
			data_table.addColumn('string', 'Call Time');
			data_table.addColumn('string', 'Call Duration');
			data_table.addColumn('number', 'Amount');
			data_table.addColumn('string', 'In/Out');
			data_table.addColumn('string', 'Roaming Call');
			data_table.addColumn('string', 'SMS');
			
			for(var i=0; i< jsonData.length; i++){
				var amt = Math.round(jsonData[i].Amount * 100) / 100;
				data_table.addRow([jsonData[i].Name,jsonData[i].CallDate,jsonData[i].CallTime,jsonData[i].CallDuration,amt,jsonData[i].CallDirection,jsonData[i].IsRoaming,jsonData[i].IsSMS]);
			}
			
			//*
			var control = new google.visualization.ControlWrapper({
					containerId: 'drill-down-filter',
					controlType: 'StringFilter',
					options: {
						filterColumnIndex: 0,
						ui: {}
					}
			});
			
			var chart = new google.visualization.ChartWrapper({
					containerId: 'drill-down-chart',
					chartType: 'Table',
					view: {
						columns: [0, 1, 2, 3, 4, 5,6,7]
					},
					options: {
						page: 'enable',
						pageSize: 20
					}
			});
			
			var dashboard = new google.visualization.Dashboard(document.getElementById('drill-down-dashboard'));
			dashboard.bind([control], [chart]);
			dashboard.draw(data_table);
			//*/
			//var table = new google.visualization.Table(document.getElementById('drill-down-area'));
			//table.draw(data_table, {showRowNumber: false});
		
		});
		
	});
	
	// Top Caller Details
	$("#top-caller-details").click(function(){
		
		var bill_id = $( "#bill-list" ).val();
		if(bill_id === "Select"){
			return;
		}
		
		$.post("pba_utility.php", {code:"Top-Caller-Details", bill_id:bill_id},
		function(data,status){
		
			var jsonData = JSON.parse(data);
			var data_table = new google.visualization.DataTable();
			
			data_table.addColumn('string', 'Call Date');
			data_table.addColumn('string', 'Call Time');
			data_table.addColumn('string', 'Call Duration');
			data_table.addColumn('number', 'Amount');
			data_table.addColumn('string', 'In/Out');
			data_table.addColumn('string', 'Roaming Call');
			data_table.addColumn('string', 'SMS');
			
			for(var i=0; i< jsonData.length; i++){
				var amt = Math.round(jsonData[i].Amount * 100) / 100;
				data_table.addRow([jsonData[i].CallDate,jsonData[i].CallTime,jsonData[i].CallDuration,amt,jsonData[i].CallDirection,jsonData[i].IsRoaming,jsonData[i].IsSMS]);
			}
			
			var table = new google.visualization.Table(document.getElementById('drill-down-chart'));
			table.draw(data_table, {showRowNumber: true, page: 'enable', pageSize: 20});
		
		});
		
	});
	
	// Data usage Details
	$("#data-usage-details").click(function(){
		
		var bill_id = $( "#bill-list" ).val();
		if(bill_id === "Select"){
			return;
		}
		
		$.post("pba_utility.php", {code:"Data-Usage-Details", bill_id:bill_id},
		function(data,status){
		
			var jsonData = JSON.parse(data);
			var data_table = new google.visualization.DataTable();
			
			data_table.addColumn('string', 'Call Date');
			data_table.addColumn('string', 'Call Time');
			data_table.addColumn('number', 'Volume (MB)');
			data_table.addColumn('number', 'Amount');
			
			for(var i=0; i< jsonData.length; i++){
				var vol = Math.round(jsonData[i].Volume * 100) /100;
				var amt = Math.round(jsonData[i].Amount * 100) /100;
				data_table.addRow([jsonData[i].CallDate,jsonData[i].CallTime,vol,amt]);
			}
			
			var table = new google.visualization.Table(document.getElementById('drill-down-chart'));
			table.draw(data_table, {showRowNumber: true, page: 'enable', pageSize: 20});
		
		});
		
	});

});