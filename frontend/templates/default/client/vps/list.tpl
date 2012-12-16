<table class="vpslist">
	<tr>
		<th></th>
		<th>{%!list-column-hostname}</th>
		<th>{%!list-column-platform}</th>
		<th>{%!list-column-node}</th>
		<th>{%!list-column-disk}</th>
		<th>{%!list-column-ram}</th>
		<th>{%!list-column-template}</th>
	</tr>
	{%foreach container in containers}
		<tr class="clickable" data-url="/{%?container[id]}/">
			<td class="container-status">
				{%if container[status] == running}
					<img src="/templates/default/static/images/status/online.png" alt="{%!list-status-running}">
				{%elseif container[status] == stopped}
					<img src="/templates/default/static/images/status/offline.png" alt="{%!list-status-stopped}">
				{%elseif container[status] == suspended}
					<img src="/templates/default/static/images/status/suspended.png" alt="{%!list-status-suspended}">
				{%else}
					<img src="/templates/default/static/images/status/unknown.png" alt="{%!list-status-unknown}">
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
				<a href="/{%?container[id]}/">
					<span class="nodename">{%?container[node]}</span>
					<span class="hostname">({%?container[node-hostname]})</span>
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
</table>
