<h2>{%!title-admin-addvps}</h2>

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

<form method="post" action="/admin/vpses/add/" class="add dark">
	<div class="field">
		<label for="form_addvps_node">{%!addvps-node}</label>
		{%select group="addvps" name="node"}
			{%foreach node in nodes}
				{%option value="(?node[id])" text="(?node[name]) ((?node[location]))"}
			{%/foreach}
		{%/select}
		<div class="clear"></div>
	</div>
	
	<div class="field">
		<label for="form_addvps_template">{%!addvps-template}</label>
		{%select group="addvps" name="template"}
			{%foreach template in templates}
				{%option value="(?template[id])" text="(?template[name])"}
			{%/foreach}
		{%/select}
		<div class="clear"></div>
	</div>
	
	<div class="field">
		<label for="form_addvps_user">{%!addvps-user}</label>
		{%select group="addvps" name="user"}
			{%foreach user in users}
				{%option value="(?user[id])" text="(?user[username]) (#(?user[id]))"}
			{%/foreach}
		{%/select}
		<div class="clear"></div>
	</div>
	
	<div class="field">
		<label for="form_addnode_diskspace">{%!addvps-diskspace}</label>
		{%input type="text" group="addvps" name="diskspace"}
		<div class="clear"></div>
	</div>
	
	<div class="field">
		<label for="form_addnode_guaranteed">{%!addvps-guaranteed}</label>
		{%input type="text" group="addvps" name="guaranteed"}
		<div class="clear"></div>
	</div>
	
	<div class="field">
		<label for="form_addnode_burstable">{%!addvps-burstable}</label>
		{%input type="text" group="addvps" name="burstable"}
		<div class="clear"></div>
	</div>
	
	<div class="field">
		<label for="form_addnode_cpucount">{%!addvps-cpucount}</label>
		{%input type="text" group="addvps" name="cpucount"}
		<div class="clear"></div>
	</div>
	
	<div class="field">
		<label for="form_addnode_traffic">{%!addvps-traffic}</label>
		{%input type="text" group="addvps" name="traffic"}
		<div class="clear"></div>
	</div>
	
	<div class="field">
		<label for="form_addnode_hostname">{%!addvps-hostname}</label>
		{%input type="text" group="addvps" name="hostname"}
		<div class="clear"></div>
	</div>
	
	<div class="field">
		<div class="filler"></div>
		<button type="submit" name="submit">{%!button-admin-addvps}</button>
		<div class="clear"></div>
	</div>
</form>
