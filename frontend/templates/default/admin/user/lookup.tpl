<div class="toolbar">
	<a href="/admin/user/{%?id}/add/">{%!toolbar-createvps}</a>
	<a href="/admin/user/{%?id}/edit/">{%!toolbar-edituser}</a>
	<div class="clear"></div>
</div>

<h2>{%!title-admin-userinfo}</h2>

<table class="userinfo vertical">
	<tr>
		<th>{%!admin-title-id}</th>
		<td>{%?id}</td>
	</tr>
	<tr>
		<th>{%!admin-title-username}</th>
		<td>{%?username}</td>
	</tr>
	<tr>
		<th>{%!admin-title-email}</th>
		<td>{%?email}</td>
	</tr>
	<tr>
		<th>{%!admin-title-accesslevel}</th>
		<td>
			{%if accesslevel == 1}
				{%!admin-level-enduser}
			{%/if}{%if accesslevel == 10}
				{%!admin-level-reseller}
			{%/if}{%if accesslevel == 20}
				{%!admin-level-nodeadmin}
			{%/if}{%if accesslevel == 30}
				{%!admin-level-masteradmin}
			{%/if}
		</td>
	</tr>
	<tr>
		<th>{%!admin-title-vpses}</th>
		<td>{%?vpscount}</td>
	</tr>
</table>

<h3>{%!header-admin-user-vpses}</h3>
<table class="vpslist">
	<tr>
		<th></th>
		<th>{%!list-column-hostname}</th>
		<th>{%!list-column-platform}</th>
		<th>{%!list-column-disk}</th>
		<th>{%!list-column-ram}</th>
		<th>{%!list-column-template}</th>
	</tr>
	{%foreach vps in vpses}
		<tr class="clickable" data-url="/{%?vps[id]}/">
			<td class="vps-status">
				{%if vps[status] == running}
					<img src="/templates/default/static/images/status/online.png" alt="{%!list-status-running}">
				{%elseif vps[status] == stopped}
					<img src="/templates/default/static/images/status/offline.png" alt="{%!list-status-stopped}">
				{%elseif vps[status] == suspended}
					<img src="/templates/default/static/images/status/suspended.png" alt="{%!list-status-suspended}">
				{%else}
					<img src="/templates/default/static/images/status/unknown.png" alt="{%!list-status-unknown}">
				{%/if}
			<td>
				<a href="/{%?vps[id]}/">
					{%?vps[hostname]}
				</a>
			</td>
			<td>
				<a href="/{%?vps[id]}/">
					{%if vps[virtualization-type] == 1}
						OpenVZ
					{%/if}{%if vps[virtualization-type] == 2}
						Xen PV
					{%/if}{%if vps[virtualization-type] == 3}
						Xen HVM
					{%/if}{%if vps[virtualization-type] == 4}
						KVM
					{%/if}
				</a>
			</td>
			<td>
				{%?vps[diskspace]}
				<span class="unit">{%?vps[diskspace-unit]}</span>
			</td>
			<td>
				{%?vps[guaranteed-ram]}
				<span class="unit">{%?vps[guaranteed-ram-unit]}</span>
			</td>
			<td>{%?vps[template]}</td>
		</tr>
	{%/foreach}
</table>
