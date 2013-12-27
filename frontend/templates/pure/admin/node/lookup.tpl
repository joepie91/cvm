<h2>{%!title-admin-nodeinfo}</h2>

<div class="pure-menu pure-menu-open pure-menu-horizontal submenu">
	<ul>
		<li><a href="/admin/node/{%?id}/add/"><i class="fa fa-fw fa-asterisk"></i> {%!toolbar-createvps}</a></li>
		<li><a href="/admin/node/{%?id}/edit/"><i class="fa fa-fw fa-pencil"></i> {%!toolbar-editnode}</a></li>
	</ul>
</div>

<section>
	<table class="pure-table pure-table-bordered stretch">
		<tr>
			<th class="vertical">{%!list-column-nodeid}</th>
			<td>{%?id}</td>
		</tr>
		<tr>
			<th class="vertical">{%!list-column-hostname}</th>
			<td>{%?hostname}</td>
		</tr>
		<tr>
			<th class="vertical">{%!list-column-location}</th>
			<td>{%?location}</td>
		</tr>
	</table>
</section>

<section>
	<h3>{%!header-admin-node-vpses}</h3>
	<table class="pure-table pure-table-bordered stretch">
		<thead>
			<tr>
				<th></th>
				<th>{%!list-column-hostname}</th>
				<th>{%!list-column-platform}</th>
				<th>{%!list-column-disk}</th>
				<th>{%!list-column-ram}</th>
				<th>{%!list-column-template}</th>
			</tr>
		</thead>
		<tbody>
			{%if isset|vpses == true}
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
						</td>
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
			{%/if}
		</tbody>
	</table>
</section>
