<h1>{%!title-password}</h1>


<form method="post" action="/{%?id}/password/" class="root-password dark">
	<div class="field">
		<label for="form_password_password">{%!password-field-password}</label>
		{%input type="text" group="password" name="password"}
		<div class="clear"></div>
	</div>

	<div class="field">
		<label for="form_password_confirm">{%!password-field-confirm}</label>
		{%input type="text" group="password" name="confirm"}
		<div class="clear"></div>
	</div>
	
	<div class="field">
		<div class="filler"></div>
		<button type="submit" name="submit">{%!button-password}</button>
		<div class="clear"></div>
	</div>
</form>
