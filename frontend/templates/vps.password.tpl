<h1>{%!title-password}</h1>

<form method="post" action="/{%?id}/password/">
	<label class="col_4" for="field_password">{%!password-field-password}</label>
	<input class="col_4" type="password" id="field_password" name="password">
	<div class="clear"></div>
	
	<label class="col_4" for="field_confirm">{%!password-field-confirm}</label>
	<input class="col_4" type="password" id="field_confirm" name="confirm">
	<div class="clear"></div>
	
	<div class="col_4"></div>
	<button class="col_4" type="submit" name="submit">{%!button-password}</button>
</form>
