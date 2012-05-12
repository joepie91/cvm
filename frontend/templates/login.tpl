<h1>{%!title-login}</h1>

{%?error}

<form method="post" action="/login/" class="col_12">
	<div class="col_3"></div>
	<label class="col_2" for="field_username">{%!login-username}</label>
	<input class="col_4" type="text" name="username" id="field_username" value="">
	<div class="clear"></div>

	<div class="col_3"></div>
	<label class="col_2" for="field_password">{%!login-password}</label>
	<input class="col_4" type="password" name="password" id="field_password">
	<div class="clear"></div>

	<div class="col_7"></div>
	<button class="col_2" type="submit" name="submit">{%!button-login}</button>
</form>
