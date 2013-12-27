<div class="narrow-wrapper" id="form_login">
	{%?error}

	<form class="pure-form pure-form-aligned" method="post" action="/login/">
		<fieldset>
			<div class="pure-control-group">
				<label for="form_login_username">{%!login-username}</label>
				{%input type="text" group="login" name="username"}
			</div>
			<div class="pure-control-group">
				<label for="form_login_password">{%!login-password}</label>
				{%input type="password" group="login" name="password"}
			</div>
			<div class="pure-controls">
				<button type="submit" name="submit" class="pure-button pure-button-primary">{%!button-login}</button>
			</div>
		</fieldset>
	</form>
</div>
