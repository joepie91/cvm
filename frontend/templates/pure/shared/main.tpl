<!doctype html>
<html>
	<head>
		<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.3.0/pure-min.css">
		<link rel="stylesheet" href="/templates/pure/static/css/font-awesome.css">
		<link rel="stylesheet" href="/templates/pure/static/css/cvm.css">
	</head>
	<body>
		<div class="wrapper">
			<div class="header">
				<img src="/templates/pure/static/images/logo.png" class="logo">
			</div>
			{%if logged-in == true}
				<div class="menu pure-menu pure-menu-open pure-menu-horizontal">
					<ul>
						<li><a href="/">VPS overview</a></li>
						<li><a href="/account/">Your account</a></li>
						<li><a href="/admin/">Admin</a></li>
					</ul>
				</div>
			{%/if}
			<div class="contents">
				{%?main}
			</div>
		</div>
	</body>
</html>
