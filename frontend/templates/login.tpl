<h1>{%!title-login}</h1>

{%?error}

<form method="post" action="/login/" class="login">
	<div class="field">
		<label for="form_login_username">{%!login-username}</label>
		{%input type="text" group="login" name="username"}
		<div class="clear"></div>
	</div>

	<div class="field">
		<label for="form_login_password">{%!login-password}</label>
		{%input type="password" group="login" name="password"}
		<div class="clear"></div>
	</div>
	
	<div class="field">
		<div class="filler">
			<a href="/forgot/">Forgot?</a>
		</div>
		<button type="submit" name="submit">{%!button-login}</button>
		<div class="clear"></div>
	</div>
</form>
