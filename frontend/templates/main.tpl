<!doctype html>
<html>
	<head>
		<title>CVM</title>
		<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400,700">
		<link rel="stylesheet" href="/css/cvm.css?1">
		<link rel="stylesheet" href="/css/kickstart-grid.css" media="all">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		<script src="/js/prettify.js"></script>
		<script src="/js/kickstart.js"></script>
		<script src="/js/cvm.js?3"></script>
	</head>
	<body>
		<div class="wrapper">
			<div class="header">
				<img src="/images/logo.png">
				{%if logged-in == true}
					<div class="userbox">
						<div>{%!userbox-loggedin}</div>
						{%if accesslevel >= 20}
							<a href="/admin/">{%!userbox-admin}</a>
						{%/if}
						<a href="/account/">{%!userbox-account}</a>
						<a href="/">{%!userbox-list}</a>
						<a href="/logout/">{%!userbox-logout}</a>
					</div>
				{%/if}
			</div>
			<div class="main {%?main-class}">
				{%?main}
			</div>
			<div class="footer">
				{%!footer}
			</div>
		</div>
		<img class="preload" src="/images/loading.gif">
		{%?generation}
	</body>
</html>
