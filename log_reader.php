<?php
$dt = new DateTime();
$dt = $dt->format('Y-m-d H:i:s');
?>

<!DOCTYPE html>
<html lang="en-US" ng-app="jsonapp">
<head>
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
	<base href="http://localhost/monogame_json_reader/"></base>
	<title>Log Reader</title>
	<meta charset="UTF-8">
	<meta name="description" content="Content Description">
	<meta name="keywords" content="HTML, CSS, XML, JavaScript">
	<meta name="author" content="aee">
	<!--meta http-equiv="Content-Type" content="text/html; charset=utf-8"-->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<![endif]-->
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>		<!-- JQuery -->
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.min.js"></script>    <!-- AngularJS -->
	
	<!-- cache workaround -->
	<?php print"<script type='text/javascript' src='scripts/jscripts.js?$dt'></script>";?>		    <!-- Jscript file -->
	<?php print"<link  rel='stylesheet' href='css/styles.css?$dt' type='text/css' />";?>	    	<!-- CSS file -->
	
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>      	<!-- Google Charts -->
</head>

<body ng-controller="jsonctrl">
	<div id="main" class="clearfix">
		<div class="heading">Log Reader</div>
		<br/>
		<div id="log_container" ng-cloak>
			<div style="width:100%;height:auto;">
				<textarea id="textarea_log" cols="110" rows="10" style="margin-bottom:5px; width:795px; resize: vertical;" ng-model="json_log" placeholder="paste the log text here"></textarea>
				
				<div style="text-align:center;">
					<input id="process_log" style="background:#444;width:100px; height:25px; display:inline-block; position:relative; margin-top:5px; " type="submit" value="Process" ng-click="processLog()">	
					<input id="clear_log" style="background:#444; width:100px; height:25px; display:inline-block; position:relative; margin-top:5px; " type="reset" value="Clear" ng-click="json_log = '';processing_message = 'waiting for log text...';;">	
				</div>
				
				<div id="processing_message" style="float:left; text-align:center; clear:both; width: calc(100% - 10px); {{processing_message == 'completed'?'color:green;':'';}}{{processing_message == 'no content found...'?'color:red;':'';}}">{{processing_message}}</div>
				
				<div class="separator_v2" style="text-align:center;"> Log Contents</div>
				<div style="clear:both; color:#888;text-align:center; margin:auto; width:100%; height:25px; line-height:25px; " ng-if="log_objects.length != null">total: {{(log_objects | filter:{qualifier:filter_mode}).length}} messages</div>
				<div style="clear:both; color:orange;text-align:center; margin:auto; width:100%; height:25px; line-height:25px; " ng-if="log_objects.length != null">critical: {{(log_objects | filter:{qualifier:'critical_message'}).length}} messages</div>
				<div ng-if="log_objects.length == null || log_objects.length == 0" style="width:100%;height:auto; float:left; clear:both; text-align:center; background:#444;">
					<span style="color:#888;">no data to display...</span>
				</div>
				
				<div style="clear:both; float:left; height:auto; width:100%; background:#444; text-align:center;">
						<div style="width:100%; height:auto; display:inline-block;">
							<input style="border:none;{{filter_mode == ''?'background:#429ef4;':''}}"  type="button" value="all messages"    
							ng-click="filter_mode = '';">
							<input style="border:none;{{filter_mode == 'system_message'?'background:#429ef4;':''}}"  type="button" value="system messages"     
							ng-click="filter_mode = 'system_message';">
							<input style="border:none;{{filter_mode == 'repeating_operation'?'background:#429ef4;':''}}"  type="button" value="repeating operation" 
							ng-click="filter_mode = 'repeating_operation';">
							<input style="border:none;{{filter_mode == 'critical_message'?'background:#429ef4;':''}}"  type="button" value="critical messages"   
							ng-click="filter_mode = 'critical_message';">
						</div>
						<!-- filter by content -->
						<div style="float:left; clear:both; width:100%; height: 30px; text-align:center; padding-bottom:3px;">
							<span>filter message text:</span> <input style="color:white; background-color:#333; margin-top:3px; border:none; min-width:100px; padding: 1px;" type="text" ng-model="logsearch" ng-change="processLogLimited(logsearch)">
							<input id="clear_log_filter" style="background:#333; width:100px; height:25px; display:inline-block; position:relative; margin-top:5px; " type="reset" value="Clear Filter" ng-click="logsearch = ''; processLogLimited(logsearch);">
						</div>
				</div>
				
				<div ng-repeat="l in log_objects | filter:{qualifier:filter_mode || undefined}:true" style="height:auto; margin-top:5px; width:100%; float:left; clear:both;" >
					<div class="{{l.qualifier}}">{{l.qualifier}}</div>
					<span style="float:right;color:white;"><span class="description">time:</span> {{l.timestamp}}</span>
					<div class="clearfix" style="float:left; padding:3px; background:#222;width:100%; height:auto;">
						<div class="log_line" style="height:auto; text-align:center;"><span class="description">message:</span>  {{l.message}}</div>
						<div class="log_line" style="height:auto; text-align:center;"><span class="description">call stack:</span>  {{l.caller}}</div>
						<div ng-repeat="(key, data) in l.tracker" class="log_line_tracking clearfix" style="float:left;">
							<span class="description">{{key}}</span>: <span>{{data}}</span>
						</div>
						<div class="log_line_tracking" style="text-align:left; color: #429ef4; float:left; padding-left:0px;">tags:</div>
						<div ng-repeat="tag in l.tags" class="log_line_tracking clearfix" style="float:left;">{{tag}}</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
