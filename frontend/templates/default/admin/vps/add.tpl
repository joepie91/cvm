<h2>{%!title-admin-addcontainer}</h2>

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

<form method="post" action="/admin/containers/add/" class="add dark">
	<div class="field">
		<label for="form_addcontainer_node">{%!addcontainer-node}</label>
		{%select group="addcontainer" name="node"}
			{%foreach node in nodes}
				{%option value="(?node[id])" text="(?node[name]) ((?node[location]))"}
			{%/foreach}
		{%/select}
		<div class="clear"></div>
	</div>
	
	<div class="field">
		<label for="form_addcontainer_template">{%!addcontainer-template}</label>
		{%select group="addcontainer" name="template"}
			{%foreach template in templates}
				{%option value="(?template[id])" text="(?template[name])"}
			{%/foreach}
		{%/select}
		<div class="clear"></div>
	</div>
	
	<div class="field">
		<label for="form_addcontainer_user">{%!addcontainer-user}</label>
		{%select group="addcontainer" name="user"}
			{%foreach user in users}
				{%option value="(?user[id])" text="(?user[username]) (#(?user[id]))"}
			{%/foreach}
		{%/select}
		<div class="clear"></div>
	</div>
	
	<div class="field">
		<label for="form_addnode_diskspace">{%!addcontainer-diskspace}</label>
		{%input type="text" group="addcontainer" name="diskspace"}
		<div class="clear"></div>
	</div>
	
	<div class="field">
		<label for="form_addnode_guaranteed">{%!addcontainer-guaranteed}</label>
		{%input type="text" group="addcontainer" name="guaranteed"}
		<div class="clear"></div>
	</div>
	
	<div class="field">
		<label for="form_addnode_burstable">{%!addcontainer-burstable}</label>
		{%input type="text" group="addcontainer" name="burstable"}
		<div class="clear"></div>
	</div>
	
	<div class="field">
		<label for="form_addnode_cpucount">{%!addcontainer-cpucount}</label>
		{%input type="text" group="addcontainer" name="cpucount"}
		<div class="clear"></div>
	</div>
	
	<div class="field">
		<label for="form_addnode_traffic">{%!addcontainer-traffic}</label>
		{%input type="text" group="addcontainer" name="traffic"}
		<div class="clear"></div>
	</div>
	
	<div class="field">
		<label for="form_addnode_hostname">{%!addcontainer-hostname}</label>
		{%input type="text" group="addcontainer" name="hostname"}
		<div class="clear"></div>
	</div>
	
	<div class="field">
		<div class="filler"></div>
		<button type="submit" name="submit">{%!button-admin-addcontainer}</button>
		<div class="clear"></div>
	</div>
</form>
