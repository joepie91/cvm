<!doctype html>
<html>
	<head>
		<title>CVM</title>
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="/css/cvm.css?1" type="text/css">
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		<script type="text/javascript" src="/js/cvm.js?3"></script>
	</head>
	<body>
		<div class="wrapper">
			<div class="header">
				<img src="/images/logo.png">
				<div class="userbox">
					<div>You are logged in as <strong>joepie91</strong>.</div>
					<div><a href="/">Account overview</a> | <a href="/containers/">My VPSes</a></div>
				</div>
			</div>
			<div class="main <%?main-class>">
				<%?main>
			</div>
			<div class="footer">
				CPHP is a free and open source VPS control panel. <a href="http://cvm.cryto.net/trac/wiki/Contribute">Want to contribute?</a>
			</div>
		</div>
		<img class="preload" src="images/loading.gif">
	</body>
</html>
