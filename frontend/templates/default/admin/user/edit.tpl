<h2>{%!title-admin-edituser}</h2>

{%if isempty|errors == false}
	<div class="errorhandler error-error">
		<div class="error-title">{%!error-form}</div>
		<div class="error-message">
			<ul>
				{%foreach error in errors}
					<li>{%?error}</li>
				{%/foreach}
			</ul>
		</div>
	</div>
{%/if}

<form method="post" action="/admin/user/{%?id}/edit/" class="add dark">
	<div class="field">
		<label for="form_edituser_username">{%!edituser-username}</label>
		{%input type="text" group="edituser" name="username"}
		<div class="clear"></div>
	</div>
	
	<div class="field">
		<label for="form_edituser_email">{%!edituser-email}</label>
		{%input type="text" group="edituser" name="email"}
		<div class="clear"></div>
	</div>
	
	<div class="field">
		<label for="form_edituser_access">{%!edituser-access}</label>
		{%select type="text" group="edituser" name="access"}
			{%option value="1" text="{%!admin-level-enduser}"}
			<!-- {%option value="10" text="{%!admin-level-reseller}"} -->
			{%option value="20" text="{%!admin-level-nodeadmin}"}
			{%option value="30" text="{%!admin-level-masteradmin}"}
		{%/select}
		<div class="clear"></div>
	</div>
	
	<div class="field">
		<div class="filler"></div>
		<button type="submit" name="submit">{%!button-admin-edituser}</button>
		<div class="clear"></div>
	</div>
</form>
