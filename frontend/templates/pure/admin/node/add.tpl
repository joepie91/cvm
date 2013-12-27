<h2>{%!title-admin-addnode}</h2>

{%if isempty|errors == false}
	<div class="error">
		<div class="title"><i class="fa fa-times-circle"></i> {%!error-form}</div>
		<div class="message">
			<ul>
				{%foreach error in errors}
					<li>{%?error}</li>
				{%/foreach}
			</ul>
		</div>
	</div>
{%/if}

<form enctype="multipart/form-data" method="post" action="/admin/nodes/add/" class="pure-form pure-form-aligned">
	<div class="pure-control-group">
		<label for="form_addnode_name">{%!addnode-name}</label>
		{%input type="text" group="addnode" name="name"}
	</div>
	
	<div class="pure-control-group">
		<label for="form_addnode_hostname">{%!addnode-hostname}</label>
		{%input type="text" group="addnode" name="hostname"}
	</div>
	
	<div class="pure-control-group">
		<label for="form_addnode_location">{%!addnode-location}</label>
		{%input type="text" group="addnode" name="location"}
	</div>
	
	<div class="pure-control-group">
		<label for="form_addnode_customkey">{%!addnode-customkeypair}</label>
		{%input type="checkbox" group="addnode" name="customkey" data-enable-group="customkey" class="enabler"}
	</div>
	
	<div class="disabled pure-control-group" data-disabled-group="customkey">
		<label for="form_addnode_publickey">{%!addnode-publickey}</label>
		{%input type="file" group="addnode" name="publickey" disabled="disabled"}
	</div>
	
	<div class="disabled pure-control-group" data-disabled-group="customkey">
		<label for="form_addnode_privatekey">{%!addnode-privatekey}</label>
		{%input type="file" group="addnode" name="privatekey" disabled="disabled"}
	</div>
	
	<div class="pure-controls">
		<button type="submit" name="submit">{%!button-admin-addnode}</button>
	</div>
</form>
