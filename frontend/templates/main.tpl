<!doctype html>
<html>
	<head>
		<title>CVM</title>
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="css/cvm.css" type="text/css">
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		<script type="text/javascript" src="js/cvm.js?1"></script>
	</head>
	<body>
		<div class="wrapper">
			<div class="header">
				<img src="images/logo.png">
			</div>
			<div class="main">
				<div class="sidebar">
					<a class="button" id="button_overview" href="#">Overview</a>
					<a class="button" id="button_statistics" href="#">Statistics</a>
					<a class="button" id="button_reinstall" href="#">Reinstall</a>
					<a class="button" id="button_backup" href="#">Backups</a>
					<a class="button" id="button_webshell" href="#">WebShell</a>
					<a class="button" id="button_ip" href="#">IP Allocation</a>
					<a class="button" id="button_alerts" href="#">Alerts</a>
					<a class="button" id="button_api" href="#">API</a>
				</div>
				<h1>Overview</h1>
				
				<!-- <h2>Consolidated traffic example</h2> -->
				<div class="quota">
					<div class="quota-item">
						<h3>Disk space</h3>
						<div class="quota-bar">
							<div class="quota-bar-inner" style="width: 55%;"></div>
							<div class="quota-bar-label">55/100GB</div>
						</div>
					</div>
					<div class="quota-item">
						<h3>RAM</h3>
						<div class="quota-bar">
							<div class="quota-bar-inner" style="width: 24%;"></div>
							<div class="quota-bar-label">241/1024MB</div>
						</div>
					</div>
					<div class="quota-item last">
						<h3>Traffic</h3>
						<div class="quota-bar">
							<div class="quota-bar-inner" style="width: 8%;"></div>
							<div class="quota-bar-label">80/1000GB</div>
						</div>
					</div>
					<div class="clear"></div>
				</div>
				
				<div class="controlbox">
					<a class="controlbutton button-loader" href="?action=start">
						<img src="images/button_start.png" class="button-icon">
						Start VPS
					</a>
					<a class="controlbutton button-loader" href="?action=restart">
						<img src="images/button_restart.png" class="button-icon">
						Restart VPS
					</a>
					<a class="controlbutton button-loader last" href="?action=stop">
						<img src="images/button_stop.png" class="button-icon">
						Stop VPS
					</a>
					<div class="clear"></div>
				</div>
				
				<div class="infobox">
					<h2>VPS configuration</h2>
					<table class="vpsinfo">
						<tr>
							<th>Status</th>
							<td><span class="online">Running</span> <span class="offline">Stopped</span> <span class="suspended">Suspended</span> </td>
						</tr>
						<tr>
							<th>Server location</th>
							<td><%?server-location></td>
						</tr>
						<tr>
							<th>Operating system</th>
							<td><%?operating-system></td>
						</tr>
						<tr>
							<th>IPv4 Addresses</th>
							<td>98.142.213.226, 204.12.235.84</td>
						</tr>
						<tr>
							<th>IPv6 Addresses</th>
							<td>2607:f7a0:1:1::24:6</td>
						</tr>
						<tr>
							<th>Guaranteed RAM</th>
							<td><%?guaranteed-ram></td>
						</tr>
						<tr>
							<th>Burstable RAM</th>
							<td><%?burstable-ram></td>
						</tr>
						<tr>
							<th>Disk space</th>
							<td><%?disk-space></td>
						</tr>
						<tr>
							<th>Traffic</th>
							<td><%?total-traffic-limit></td>
						</tr>
						<tr>
							<th>Bandwidth</th>
							<td><%?bandwidth-limit></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</body>
</html>
