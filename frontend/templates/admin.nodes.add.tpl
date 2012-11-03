<h2>{%!title-admin-addnode}</h2>

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

<form enctype="multipart/form-data" method="post" action="/admin/nodes/add/" class="add dark">
	<div class="field">
		<label for="form_addnode_name">{%!addnode-name}</label>
		{%input type="text" group="addnode" name="name"}
		<div class="clear"></div>
	</div>
	
	<div class="field">
		<label for="form_addnode_hostname">{%!addnode-hostname}</label>
		{%input type="text" group="addnode" name="hostname"}
		<div class="clear"></div>
	</div>
	
	<div class="field">
		<label for="form_addnode_location">{%!addnode-location}</label>
		{%input type="text" group="addnode" name="location"}
		<div class="clear"></div>
	</div>
	
	<div class="field">
		<label for="form_addnode_customkey">{%!addnode-customkeypair}</label>
		{%input type="checkbox" group="addnode" name="customkey" data-enable-group="customkey" class="enabler"}
		<div class="clear"></div>
	</div>
	
	<div class="disabled field" data-disabled-group="customkey">
		<label for="form_addnode_publickey">{%!addnode-publickey}</label>
		{%input type="file" group="addnode" name="publickey" disabled="disabled"}
		<div class="clear"></div>
	</div>
	
	<div class="disabled field" data-disabled-group="customkey">
		<label for="form_addnode_privatekey">{%!addnode-privatekey}</label>
		{%input type="file" group="addnode" name="privatekey" disabled="disabled"}
		<div class="clear"></div>
	</div>
	
	<div class="field">
		<div class="filler"></div>
		<button type="submit" name="submit">{%!button-admin-addnode}</button>
		<div class="clear"></div>
	</div>
</form>
