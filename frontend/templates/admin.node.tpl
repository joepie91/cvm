<h2>{%!title-admin-nodeinfo}</h2>

<table class="nodeinfo vertical">
	<tr>
		<th>{%!list-column-nodeid}</th>
		<td>{%?id}</td>
	</tr>
	<tr>
		<th>{%!list-column-hostname}</th>
		<td>{%?hostname}</td>
	</tr>
	<tr>
		<th>{%!list-column-location}</th>
		<td>{%?location}</td>
	</tr>
</table>

<h3>{%!header-admin-node-containers}</h3>
<table class="vpslist">
	<tr>
		<th></th>
		<th>{%!list-column-hostname}</th>
		<th>{%!list-column-platform}</th>
		<th>{%!list-column-disk}</th>
		<th>{%!list-column-ram}</th>
		<th>{%!list-column-template}</th>
	</tr>
	{%if isset|containers == true}
		{%foreach container in containers}
			<tr class="clickable" data-url="/{%?container[id]}/">
				<td class="container-status">
					{%if container[status] == running}
						<img src="/images/icon_online.png" alt="{%!list-status-running}">
					{%/if}{%if container[status] == stopped}
						<img src="/images/icon_offline.png" alt="{%!list-status-stopped}">
					{%/if}{%if container[status] == suspended}
						<img src="/images/icon_suspended.png" alt="{%!list-status-suspended}">
					{%/if}
				</td>
				<td>
					<a href="/{%?container[id]}/">
						{%?container[hostname]}
					</a>
				</td>
				<td>
					<a href="/{%?container[id]}/">
						{%if container[virtualization-type] == 1}
							OpenVZ
						{%/if}{%if container[virtualization-type] == 2}
							Xen PV
						{%/if}{%if container[virtualization-type] == 3}
							Xen HVM
						{%/if}{%if container[virtualization-type] == 4}
							KVM
						{%/if}
					</a>
				</td>
				<td>
					{%?container[diskspace]}
					<span class="unit">{%?container[diskspace-unit]}</span>
				</td>
				<td>
					{%?container[guaranteed-ram]}
					<span class="unit">{%?container[guaranteed-ram-unit]}</span>
				</td>
				<td>{%?container[template]}</td>
			</tr>
		{%/foreach}
	{%/if}
</table>
