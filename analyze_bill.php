<?php

	require( 'pba-functions.php' );

	sessionStart();
		
?>

<!DOCTYPE html>
<html lang="en">

<head>

	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
	
	<title>Analyze your Mobile Bill</title>
	
	<!-- Bootstrap Core CSS -->
    <!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
	
	<!-- Font Awesome CSS -->
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
	
	<!-- Custom CSS -->
    <link href="dist/css/sb-admin-2.css" rel="stylesheet">
	
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	
	<script type="text/javascript">
		// Load the Visualization API and the piechart package.
		google.load('visualization', '1.1', {'packages':['corechart']});
		google.load('visualization', '1.1', {'packages':['controls']});
		google.load('visualization', '1.1', {'packages':['table']});
	</script>
	
</head>


<body>
	
	<?php include_once("google_analytics.php") ?>
	
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	
    <!-- Bootstrap Core JavaScript -->
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    
	<!-- Application JavaScript -->
    <script src="js/analyze_bill.js"></script>
	
	<div id="wrapper">
	
		<!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="http://www.ayansh.com">Ayansh TechnoSoft</a>
            </div>
            <!-- /.navbar-header -->



            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li>
                            <a href="index.php"><i class="fa fa-edit fa-fw"></i> Get Started</a>
                        </li>
						<li>
                            <a href="analyze_bill.php"><i class="fa fa-pie-chart fa-fw"></i>Analyze your Bill</a>
                        </li>
						<li>
                            <a href="how_it_works.php"><i class="fa fa-wrench fa-fw"></i>How does it work</a>
                        </li>
                    </ul>
					
					<br><br><br>
					<div class="panel panel-yellow">
						<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
						<!-- Phone_Bill_Analyzer -->
						<ins class="adsbygoogle"
							 style="display:block"
							 data-ad-client="ca-pub-4571712644338430"
							 data-ad-slot="2790065906"
							 data-ad-format="auto"></ins>
						<script>
						(adsbygoogle = window.adsbygoogle || []).push({});
						</script>
					</div>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>
		
		<!-- Page Content -->
        <div id="page-wrapper">

			<div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Analyze your Phone Bill</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
		
			<div class="row">
			
				<div class="col-lg-5">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Upload phone Bill
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="form-group">
								<label>Select your Telecom Provider</label>
								<select class="form-control" id="bill-type">
										<option value="STPPM">SingTel Post paid Mobile - Singapore</option>
										<option value="APPM">AirTel Post paid Mobile - India</option>
										<option value="VPPM">Vodafone Post paid Mobile - India</option>
										<option value="RPPM">Reliance Post paid Mobile - India</option>
										<option value="TDPPM">Tata Docmo Post paid Mobile - India</option>
								</select>
							</div>
							<div class="form-group">
								<label>Upload Phone bill in PDF Format</label>
								<input type="file" id="phone-bill">
							</div>
							<button id="upload-phone-bill" type="submit" class="btn btn-default">Upload</button>
							<div id="upload-message-danger" class="text-danger"><i class="fa fa-spinner fa-pulse"></i>  Reading Bill.... Please wait...</div>
							<div id="upload-message-success" class="text-success"></div>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
				
				<div class="col-lg-1">
				<h3>OR</h3>
				</div>
				
				<div class="col-lg-5">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Sync from Mobile App
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
							<p class="lead">Your unique session ID is: <strong><?php echo getSessionID(); ?></strong></p>
							<p class="text-info">Use this session ID to synchronize data from your mobile securely. After synchronizing from mobile, reload this page.</p>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
				
				<div class="col-lg-12">
					<div class="panel panel-default">
						<div class="panel-heading">
                            Select Bill
                        </div>
						<div class="panel-body">
							<div class="col-lg-3 col-md-6">
								<div class="form-group">
									<select class="form-control" id="bill-list">
										<option>Select Bill</option>
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<div class="col-lg-4 col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <span class="glyphicon glyphicon-user gi-5x" aria-hidden="true"></span>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge" id="top_caller_name"></div>
                                    <div>Top Caller</div>
                                </div>
                            </div>
                        </div>
                        <a href="#" id="top-caller-details">
                            <div class="panel-footer">
                                <span class="pull-left">Call Details</span>
                                <span class="pull-right glyphicon glyphicon-circle-arrow-right"></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
				
				<div class="col-lg-4 col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <span class="glyphicon glyphicon-usd gi-5x" aria-hidden="true"></span>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge" id="total_call_amt"></div>
                                    <div>Call Amount</div>
                                </div>
                            </div>
                        </div>
                        <a href="#" id="itemized-bill-details">
                            <div class="panel-footer">
                                <span class="pull-left">Itemized Bill</span>
                                <span class="pull-right glyphicon glyphicon-circle-arrow-right"></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
				
				<div class="col-lg-4 col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <span class="glyphicon glyphicon-sort gi-5x" aria-hidden="true"></span>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge" id="data_usage"></div>
                                    <div>Data Usage</div>
                                </div>
                            </div>
                        </div>
                        <a href="#" id="data-usage-details">
                            <div class="panel-footer">
                                <span class="pull-left">Usage Details</span>
                                <span class="pull-right glyphicon glyphicon-circle-arrow-right"></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
				
				<div class="col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Top 5 Callers
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div id="top-5-callers"></div>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
				
				<div class="col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Call amount by Group
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div id="call-amt-by-group"></div>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
			
			</div>
			
			<div class="row">
				<div class="col-lg-12">
					<div id="drill-down-dashboard">
						<div id="drill-down-filter"></div>
						<div id="drill-down-chart"></div>
					</div>
                </div>
			</div>
		
        </div>
        <!-- /#page-wrapper -->
	
	</div>
	
</body>

</html>