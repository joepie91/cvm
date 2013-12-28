<h2>{%!title-admin-addvps}</h2>

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

<form method="post" action="/admin/vpses/add/" class="pure-form pure-form-aligned">
	<div class="pure-control-group">
		<label for="form_addvps_node">{%!addvps-node}</label>
		{%select group="addvps" name="node"}
			{%foreach node in nodes}
				{%option value="(?node[id])" text="(?node[name]) ((?node[location]))"}
			{%/foreach}
		{%/select}
	</div>
	
	<div class="pure-control-group">
		<label for="form_addvps_template">{%!addvps-template}</label>
		{%select group="addvps" name="template"}
			{%foreach template in templates}
				{%option value="(?template[id])" text="(?template[name])"}
			{%/foreach}
		{%/select}
	</div>
	
	<div class="pure-control-group">
		<label for="form_addvps_user">{%!addvps-user}</label>
		{%select group="addvps" name="user"}
			{%foreach user in users}
				{%option value="(?user[id])" text="(?user[username]) (#(?user[id]))"}
			{%/foreach}
		{%/select}
	</div>
	
	<div class="pure-control-group">
		<label for="form_addnode_diskspace">{%!addvps-diskspace}</label>
		{%input type="text" group="addvps" name="diskspace"}
	</div>
	
	<div class="pure-control-group">
		<label for="form_addnode_guaranteed">{%!addvps-guaranteed}</label>
		{%input type="text" group="addvps" name="guaranteed"}
	</div>
	
	<div class="pure-control-group">
		<label for="form_addnode_burstable">{%!addvps-burstable}</label>
		{%input type="text" group="addvps" name="burstable"}
	</div>
	
	<div class="pure-control-group">
		<label for="form_addnode_cpucount">{%!addvps-cpucount}</label>
		{%input type="text" group="addvps" name="cpucount"}
	</div>
	
	<div class="pure-control-group">
		<label for="form_addnode_traffic">{%!addvps-traffic}</label>
		{%input type="text" group="addvps" name="traffic"}
	</div>
	
	<div class="pure-control-group">
		<label for="form_addnode_hostname">{%!addvps-hostname}</label>
		{%input type="text" group="addvps" name="hostname"}
	</div>
	
	<div class="pure-controls">
		<button type="submit" name="submit" class="pure-button add">{%!button-admin-addvps}</button>
	</div>
</form>
